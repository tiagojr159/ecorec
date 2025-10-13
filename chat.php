<?php
// chat.php
// Recebe uma imagem base64, envia para a OpenAI para "melhorar" e retorna base64 da imagem resultante.

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

$body = json_decode(file_get_contents('php://input'), true);
$dataUrl = $body['image_base64'] ?? '';

if (!$dataUrl || !preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $m)) {
    http_response_code(400);
    echo json_encode(["error" => "Imagem base64 inválida."]);
    exit;
}

// 🔐 Configure sua chave por variável de ambiente de preferência
$apiKey = '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';

// Converte dataURL → binário e salva arquivo temporário
$ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
$base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
$bin = base64_decode($base64);

if ($bin === false) {
    http_response_code(400);
    echo json_encode(["error" => "Não foi possível decodificar a imagem."]);
    exit;
}

$tmpFile = tempnam(sys_get_temp_dir(), 'cap_');
$imgPath = $tmpFile . '.png'; // padroniza para png
file_put_contents($imgPath, $bin);

// Monta multipart para chamada OpenAI (edits) pedindo melhoria de qualidade
$fields = [
    'model' => 'gpt-image-1',
    // Dica ao modelo: melhorar nítidez, reduzir artefatos e manter rótulos/cores do mapa
    'prompt' => 'Improve quality (upscale 2x if helpful), enhance sharpness, reduce compression artifacts, preserve map labels and colors, keep same content and orientation.',
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
        'Authorization: Bearer ' . $apiKey
        // Não setamos Content-Type para permitir o multipart automático
    ],
    CURLOPT_POSTFIELDS => $fields,
    // Compatibilidade XAMPP local
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Limpa tempfiles
@unlink($imgPath);
@unlink($tmpFile);

if ($error) {
    http_response_code(500);
    echo json_encode(["error" => "Falha na comunicação com OpenAI: $error"]);
    exit;
}

$res = json_decode($response, true);
if (!empty($res['data'][0]['b64_json'])) {
    $enhanced = 'data:image/png;base64,' . $res['data'][0]['b64_json'];
    echo json_encode([
        "status" => "ok",
        "enhanced_base64" => $enhanced
    ]);
    exit;
}

// fallback (caso venha URL ao invés de base64)
if (!empty($res['data'][0]['url'])) {
    echo json_encode([
        "status" => "ok",
        "enhanced_url" => $res['data'][0]['url']
    ]);
    exit;
}

http_response_code(500);
echo json_encode([
    "error" => "Não foi possível obter a imagem melhorada.",
    "raw" => $res
]);
