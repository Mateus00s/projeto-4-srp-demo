<?php

use App\Infra\Database\Connection;
use App\Infra\Database\Migration;

require __DIR__ . '/autoload.php';

$dbPath = __DIR__ . '/storage/database.sqlite';
if (!is_dir(__DIR__ . '/storage')) {
    mkdir(__DIR__ . '/storage', 0777, true);
}
touch($dbPath);

$connection = new Connection($dbPath);
$migration = new Migration($connection->pdo());
$migration->up();

return $connection->pdo();