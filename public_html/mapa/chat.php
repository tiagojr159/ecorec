
<?php
/**
 * chat.php (v3 + log + imagem local condicional)
 */


$usarImagemExistente = true;




// ---------- CORS ----------
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

function respond_json(int $code, array $payload)
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

@set_time_limit(180);

// ---------- Validar método ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_json(405, ["error" => "Método não permitido. Use POST."]);
}

// ---------- Ler body ----------
$raw = file_get_contents('php://input');
if (!$raw) respond_json(400, ["error" => "Corpo da requisição vazio."]);

$body = json_decode($raw, true);
if (!is_array($body)) respond_json(400, ["error" => "JSON inválido."]);

$dataUrl = $body['image_base64'] ?? '';
if (!$dataUrl || !preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $m)) {
    respond_json(400, ["error" => "Imagem base64 inválida (dataURL esperado)."]);
}

$ext  = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
$mime = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;

$base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
$bin = base64_decode($base64);
if ($bin === false) respond_json(400, ["error" => "Falha ao decodificar a imagem."]);

$improvements = $body['improvements'] ?? [];

$prompt = "Enhance the image quality: upscale up to 2x if beneficial, improve sharpness, clarity, and color balance while preserving perspective and proportions.";

if (!empty($improvements)) {
    $prompt .= " Add the following urban improvements to the image: ";

    $improvementDescriptions = [
        'arvores' => 'plant trees',
        'grama' => 'add grass/green areas',
        'fiacao' => 'improve electrical wiring',
        'pintura' => 'add fresh paint to buildings',
        'estacao_bicicleta' => 'add bicycle stations',
        'postes_led' => 'install LED streetlights',
        'placa_solar' => 'add solar panels on rooftops',
        'ciclofaixa' => 'create bike lanes',
        'camera_seguranca' => 'install security cameras',
        'estacao_carro_eletrico' => 'add electric vehicle charging stations',
        'bicicletario' => 'add bicycle parking',
        'banco_praca' => 'place park benches',
        'rampa_cadeirante' => 'add wheelchair ramps'
    ];

    $improvementPrompts = [];
    foreach ($improvements as $improvement) {
        if (isset($improvementDescriptions[$improvement])) {
            $improvementPrompts[] = $improvementDescriptions[$improvement];
        }
    }

    if (!empty($improvementPrompts)) {
        $prompt .= implode(', ', $improvementPrompts) . '. Make these improvements look realistic and well integrated with the existing urban landscape.';
    }
}

// ================================
// NOVO BLOCO: VERIFICAR IMAGEM LOCAL
// ================================

if ($usarImagemExistente == true) {
    $nomeImagem = $body['imagem_salva'] ?? '../images/exemplo2.png';



    $bin = file_get_contents($nomeImagem);
    $base64 = base64_encode($bin);
    $enhanced = 'data:image/png;base64,' . $base64;

    $logData = [
        'browser'   => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        'host'      => gethostname(),
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        'timestamp' => date('Y-m-d H:i:s'),
        'custo_usd' => 0.00,
        'modo'      => 'imagem_existente'
    ];
    $logFile = __DIR__ . '/logs_geracao_imagem.json';
    $logEntry = json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    respond_json(200, ["status" => "ok", "enhanced_base64" => $enhanced, "note" => "imagem carregada do banco"]);
}

// ================================
// CONTINUA O CÓDIGO ORIGINAL
// ================================


if (!$apiKey) respond_json(500, ["error" => "OPENAI_API_KEY não configurada."]);
$orgId  = getenv('OPENAI_ORG_ID') ?: ($_ENV['OPENAI_ORG_ID'] ?? '');

$tmpBase = tempnam(sys_get_temp_dir(), 'cap_');
if ($tmpBase === false) respond_json(500, ["error" => "Falha ao criar arquivo temporário."]);
$imgPath = $tmpBase . '.png';
if (@file_put_contents($imgPath, $bin) === false) {
    @unlink($tmpBase);
    respond_json(500, ["error" => "Falha ao gravar imagem temporária."]);
}

$maxBytes = 18 * 1024 * 1024;
if (filesize($imgPath) > $maxBytes && extension_loaded('gd')) {
    $src = imagecreatefromstring($bin);
    if ($src !== false) {
        $w = imagesx($src);
        $h = imagesy($src);
        foreach ([0.7, 0.5] as $scale) {
            $nw = max(1, (int)($w * $scale));
            $nh = max(1, (int)($h * $scale));
            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagepng($dst, $imgPath, 6);
            imagedestroy($dst);
            if (filesize($imgPath) <= $maxBytes) break;
        }
        imagedestroy($src);
    }
}

function call_openai_images(string $url, string $apiKey, string $orgId, array $fields)
{
    $headers = ['Authorization: Bearer ' . $apiKey, 'Expect:'];
    if ($orgId) $headers[] = 'OpenAI-Organization: ' . $orgId;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_TIMEOUT => 180,
    ]);
    $resp = curl_exec($ch);
    $errn = curl_errno($ch);
    $err  = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$resp, $errn, $err, $code];
}

$fieldsEdits = [
    'model'  => 'gpt-image-1',
    'prompt' => $prompt,
    'size'   => '1024x1024',
    'n'      => 1,
    'image'  => new CURLFile($imgPath, 'image/png', 'input.png'),
];

list($response, $curlErrNo, $curlErr, $httpCode) = call_openai_images(
    'https://api.openai.com/v1/images/edits',
    $apiKey,
    $orgId,
    $fieldsEdits
);

if ($curlErrNo) {
    @unlink($imgPath);
    @unlink($tmpBase);
    respond_json(502, ["error" => "Falha na comunicação com OpenAI", "details" => $curlErr]);
}

$decoded = json_decode($response, true);

if (isset($decoded['data'][0]['b64_json']) && $decoded['data'][0]['b64_json']) {
    @unlink($imgPath);
    @unlink($tmpBase);

    $logData = [
        'browser'   => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        'host'      => gethostname(),
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        'timestamp' => date('Y-m-d H:i:s'),
        'custo_usd' => 0.06
    ];
    $logFile = __DIR__ . '/logs_geracao_imagem.json';
    $logEntry = json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    $enhanced = 'data:image/png;base64,' . $decoded['data'][0]['b64_json'];
    respond_json(200, ["status" => "ok", "enhanced_base64" => $enhanced]);
}

if ($httpCode === 403) {
    $errPayload = [
        "error"     => "OpenAI retornou erro",
        "http_code" => 403,
        "details"   => $decoded['error'] ?? $response
    ];

    $msg = '';
    if (isset($decoded['error']['message'])) $msg = strtolower($decoded['error']['message']);
    $isQuota = (strpos($msg, 'insufficient_quota') !== false) || (strpos($msg, 'credit') !== false);
    $isPerm  = (strpos($msg, 'not permitted') !== false) || (strpos($msg, 'access to the model') !== false)
        || (strpos($msg, 'your api key is not allowed') !== false);

    if ($isQuota) {
        @unlink($imgPath);
        @unlink($tmpBase);
        respond_json(403, $errPayload);
    }

    if ($isPerm) {
        $fieldsVar = [
            'model' => 'gpt-image-1',
            'n'     => 1,
            'size'  => '1024x1024',
            'image' => new CURLFile($imgPath, 'image/png', 'input.png'),
        ];

        list($resp2, $errNo2, $err2, $code2) = call_openai_images(
            'https://api.openai.com/v1/images/variations',
            $apiKey,
            $orgId,
            $fieldsVar
        );

        @unlink($imgPath);
        @unlink($tmpBase);

        if ($errNo2) {
            respond_json(502, ["error" => "Falha no fallback (variations)", "details" => $err2]);
        }

        $dec2 = json_decode($resp2, true);
        if (isset($dec2['data'][0]['b64_json']) && $dec2['data'][0]['b64_json']) {
            // ---------- Registro de Log ----------
            $logData = [
                'browser'   => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
                'host'      => gethostname(),
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
                'timestamp' => date('Y-m-d H:i:s'),
                'custo_usd' => 0.06
            ];
            $logFile = __DIR__ . '/logs_geracao_imagem.json';
            $logEntry = json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            @file_put_contents($logFile, $logEntry, FILE_APPEND);

            $enhanced = 'data:image/png;base64,' . $dec2['data'][0]['b64_json'];
            respond_json(200, ["status" => "ok", "enhanced_base64" => $enhanced, "note" => "fallback: variations"]);
        }

        respond_json($code2 ?: 403, [
            "error"       => "Sem permissão para images/edits e fallback falhou.",
            "http_code"   => $code2 ?: 403,
            "primary"     => $errPayload,
            "fallbackRaw" => $dec2 ?: $resp2
        ]);
    }

    @unlink($imgPath);
    @unlink($tmpBase);
    respond_json(403, $errPayload);
}

@unlink($imgPath);
@unlink($tmpBase);
if (isset($decoded['error'])) {
    respond_json($httpCode ?: 500, [
        "error"     => "OpenAI retornou erro",
        "http_code" => $httpCode,
        "details"   => $decoded['error']
    ]);
}

respond_json($httpCode ?: 500, [
    "error"       => "Não foi possível obter a imagem melhorada.",
    "http_code"   => $httpCode,
    "raw_response" => $decoded ?: $response
]);
