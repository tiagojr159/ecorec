<?php
// chat.php — recebe dataURL, envia para OpenAI Image Edit e devolve base64

// CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(200);
    exit();
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido."]);
    exit;
}

// 1) Ler corpo e validar dataURL
$body = json_decode(file_get_contents('php://input'), true);
$dataUrl = $body['image_base64'] ?? '';

if (!$dataUrl || !preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $m)) {
    http_response_code(400);
    echo json_encode(["error" => "Imagem base64 inválida."]);
    exit;
}

$mime = strtolower($m[1]) === 'jpeg' ? 'image/jpeg' : ('image/' . strtolower($m[1]));
$ext  = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);

$base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
$bin = base64_decode($base64);
if ($bin === false) {
    http_response_code(400);
    echo json_encode(["error" => "Não foi possível decodificar a imagem."]);
    exit;
}

// 2) Chave da OpenAI via ambiente (NUNCA deixe hardcoded!)
$apiKey = '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';
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(["error" => "OPENAI_API_KEY não configurada no ambiente."]);
    exit;
}

// 3) Salvar temporário e (opcional) reduzir se for grande
$tmpBase = tempnam(sys_get_temp_dir(), 'cap_');
$imgPath = $tmpBase . '.png'; // padroniza p/ PNG
file_put_contents($imgPath, $bin);

// Limite de segurança (~18 MB) p/ evitar 413 da API
$maxBytes = 18 * 1024 * 1024;
if (filesize($imgPath) > $maxBytes && extension_loaded('gd')) {
    $src = imagecreatefromstring($bin);
    if ($src !== false) {
        $w = imagesx($src);
        $h = imagesy($src);
        // escala proporcional para ficar < ~18MB — alvo ~70% da largura
        $scale = 0.7;
        $nw = max(1, (int)($w * $scale));
        $nh = max(1, (int)($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagepng($dst, $imgPath, 6);
        imagedestroy($dst);
        imagedestroy($src);
    }
}

// 4) Montar multipart para /v1/images/edits (gpt-image-1)
$fields = [
    // strings como strings; arquivo como CURLFile
    'model' => 'gpt-image-1',
    'prompt' => 'Improve map tile quality: upscale up to 2x if useful, enhance sharpness, reduce compression artifacts, preserve labels and colors, keep same framing and orientation.',
    'size' => '1024x1024',
    'n' => '1',
    'response_format' => 'b64_json',
    'image' => new CURLFile($imgPath, 'image/png', 'input.png'),
];

$ch = curl_init('https://api.openai.com/v1/images/edits');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        // dica: evita delay do Expect: 100-continue em alguns hosts
        'Expect:'
    ],
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_TIMEOUT => 180,
    // Em produção, mantenha verificação de SSL habilitada (true).
    // Se seu ambiente local tiver problemas de certificado, você pode desligar, mas é inseguro:
    // CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$curlErrNo = curl_errno($ch);
$curlErr   = curl_error($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5) Limpeza
@unlink($imgPath);
@unlink($tmpBase);

// 6) Tratar resposta
if ($curlErrNo) {
    http_response_code(502);
    echo json_encode(["error" => "Falha na comunicação com OpenAI: $curlErr"]);
    exit;
}

$decoded = json_decode($response, true);

// Caso sucesso com base64
if (isset($decoded['data'][0]['b64_json']) && $decoded['data'][0]['b64_json']) {
    $enhanced = 'data:image/png;base64,' . $decoded['data'][0]['b64_json'];
    echo json_encode([
        "status" => "ok",
        "enhanced_base64" => $enhanced
    ]);
    exit;
}

// Alguns ambientes trazem erro estruturado
if (isset($decoded['error'])) {
    http_response_code($httpCode ?: 500);
    echo json_encode([
        "error" => "OpenAI retornou erro",
        "details" => $decoded['error'],
        "http_code" => $httpCode,
    ]);
    exit;
}

// Fallback: se veio uma URL (pouco comum hoje)
if (isset($decoded['data'][0]['url'])) {
    echo json_encode([
        "status" => "ok",
        "enhanced_url" => $decoded['data'][0]['url']
    ]);
    exit;
}

// Último recurso: ecoar bruto para debugar
http_response_code($httpCode ?: 500);
echo json_encode([
    "error" => "Não foi possível obter a imagem melhorada.",
    "http_code" => $httpCode,
    "raw_response" => $decoded ?: $response
]);
