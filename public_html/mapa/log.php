<?php
// log.php
require_once __DIR__ . '/../header.php';

// Caminho do arquivo de log
$logFile = __DIR__ . '/logs_geracao_imagem.json';
$logs = [];
$totalGasto = 0.0;

// Lê o arquivo de log, se existir
if (file_exists($logFile)) {
    $linhas = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        $data = json_decode($linha, true);
        if (is_array($data)) {
            $logs[] = $data;
            $totalGasto += floatval($data['custo_usd'] ?? 0);
        }
    }
}
?>

<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
  }

  .log-container {
    width: 95%;
    max-width: 1000px;
    margin: 40px auto;
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }

  th {
    background-color: #007bff;
    color: white;
  }

  tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  tr:hover {
    background-color: #eaf2ff;
  }

  .total {
    margin-top: 20px;
    text-align: right;
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
  }

  .empty {
    text-align: center;
    color: #777;
    padding: 20px;
  }

  .back-link {
    display: inline-block;
    margin-top: 15px;
    color: #007bff;
    text-decoration: none;
  }

  .back-link:hover {
    text-decoration: underline;
  }
</style>

<div class="log-container">
  <h2>📜 Registro de Geração de Imagens (ChatGPT)</h2>

  <?php if (!empty($logs)): ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Navegador</th>
        <th>Computador</th>
        <th>IP</th>
        <th>Data</th>
        <th>Custo (USD)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $i => $log): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= htmlspecialchars($log['browser'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($log['host'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($log['ip'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($log['timestamp'] ?? '-') ?></td>
        <td>$<?= number_format(floatval($log['custo_usd'] ?? 0), 2)*4 ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="total">
    💰 Total gasto: $<?= number_format($totalGasto, 2)*4 ?> USD
  </div>

  <?php else: ?>
  <p class="empty">Nenhum log encontrado. Nenhuma geração de imagem foi registrada ainda.</p>
  <?php endif; ?>

  <a href="javascript:history.back()" class="back-link">⬅ Voltar</a>
</div>

<?php
require_once __DIR__ . '/../footer.php';
?>
