<?php

use App\Application\Service\CheckInService;
use App\Application\Service\CheckOutService;
use App\Application\Service\PricingService;
use App\Application\Service\ReportService;
use App\Domain\Vehicle;
use App\Domain\VehicleType;
use App\Infra\Repository\SQLiteSessionRepository;

$pdo = require __DIR__ . '/../bootstrap.php';
$repo = new SQLiteSessionRepository($pdo);
$pricing = new PricingService();
$checkIn = new CheckInService($repo);
$checkOut = new CheckOutService($repo, $pricing);
$report = new ReportService($repo);

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'checkin') {
    try {
        $vehicle = new Vehicle($_POST['plate'] ?? '', VehicleType::from($_POST['type'] ?? 'car'));
        $checkIn->execute($vehicle);
        header('Location: /');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'checkout') {
    try {
        $active = $repo->listActive();
        $id = (int) ($_POST['id'] ?? 0);
        $session = current(array_filter($active, fn($s) => $s->id() === $id));
        if (!$session) {
            throw new RuntimeException('Sessão não encontrada');
        }
        $checkOut->execute($session);
        header('Location: /');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$activeSessions = $repo->listActive();
$summary = $report->summaryByType();

?><!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estacionamento</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="max-w-4xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Controle de Estacionamento</h1>
  <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 p-3 mb-4"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-3">Entrada de Veículo</h2>
      <form method="post">
        <input type="hidden" name="action" value="checkin">
        <label class="block mb-2">Placa</label>
        <input name="plate" class="border p-2 w-full mb-3" required>
        <label class="block mb-2">Tipo</label>
        <select name="type" class="border p-2 w-full mb-3">
          <option value="car">Carro</option>
          <option value="motorcycle">Moto</option>
          <option value="truck">Caminhão</option>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Entrada</button>
      </form>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-3">Relatório de Faturamento</h2>
      <table class="w-full text-left">
        <thead>
          <tr><th class="p-2">Tipo</th><th class="p-2">Qtd</th><th class="p-2">Valor</th></tr>
        </thead>
        <tbody>
          <tr><td class="p-2">Carro</td><td class="p-2"><?= $summary['car']['count'] ?></td><td class="p-2">R$ <?= number_format($summary['car']['amount'], 2, ',', '.') ?></td></tr>
          <tr><td class="p-2">Moto</td><td class="p-2"><?= $summary['motorcycle']['count'] ?></td><td class="p-2">R$ <?= number_format($summary['motorcycle']['amount'], 2, ',', '.') ?></td></tr>
          <tr><td class="p-2">Caminhão</td><td class="p-2"><?= $summary['truck']['count'] ?></td><td class="p-2">R$ <?= number_format($summary['truck']['amount'], 2, ',', '.') ?></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white p-4 rounded shadow mt-6">
    <h2 class="font-semibold mb-3">Veículos Ativos</h2>
    <table class="w-full text-left">
      <thead>
        <tr><th class="p-2">ID</th><th class="p-2">Placa</th><th class="p-2">Tipo</th><th class="p-2">Entrada</th><th class="p-2">Ações</th></tr>
      </thead>
      <tbody>
      <?php foreach ($activeSessions as $s): ?>
        <tr>
          <td class="p-2"><?= $s->id() ?></td>
          <td class="p-2"><?= htmlspecialchars($s->vehicle()->plate()) ?></td>
          <td class="p-2"><?= $s->vehicle()->type()->value ?></td>
          <td class="p-2"><?= $s->checkInAt()->format('d/m/Y H:i') ?></td>
          <td class="p-2">
            <form method="post" style="display:inline">
              <input type="hidden" name="action" value="checkout">
              <input type="hidden" name="id" value="<?= $s->id() ?>">
              <button class="bg-green-600 text-white px-3 py-1 rounded">Registrar Saída</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>