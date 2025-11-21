<?php

namespace App\Infra\Database;

use PDO;

final class Migration
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS parking_sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                plate TEXT NOT NULL,
                type TEXT NOT NULL,
                check_in_at TEXT NOT NULL,
                check_out_at TEXT NULL,
                hours INTEGER NULL,
                amount REAL NULL
            )'
        );
        $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_sessions_active ON parking_sessions(plate, check_out_at)');
    }
}