<?php

namespace App\Infra\Repository;

use App\Application\Repository\SessionRepository;
use App\Domain\ParkingSession;
use App\Domain\Vehicle;
use App\Domain\VehicleType;
use DateTimeImmutable;
use PDO;

final class SQLiteSessionRepository implements SessionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(ParkingSession $session): ParkingSession
    {
        $stmt = $this->pdo->prepare('INSERT INTO parking_sessions (plate, type, check_in_at) VALUES (:plate, :type, :check_in_at)');
        $stmt->execute([
            ':plate' => $session->vehicle()->plate(),
            ':type' => $session->vehicle()->type()->value,
            ':check_in_at' => $session->checkInAt()->format('c'),
        ]);
        $id = (int) $this->pdo->lastInsertId();
        return new ParkingSession($id, $session->vehicle(), $session->checkInAt());
    }

    public function findActiveByPlate(string $plate): ?ParkingSession
    {
        $stmt = $this->pdo->prepare('SELECT * FROM parking_sessions WHERE plate = :plate AND check_out_at IS NULL LIMIT 1');
        $stmt->execute([':plate' => strtoupper(trim($plate))]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $this->mapRow($row);
    }

    public function checkout(ParkingSession $session, int $hours, float $amount): ParkingSession
    {
        $stmt = $this->pdo->prepare('UPDATE parking_sessions SET check_out_at = :check_out_at, hours = :hours, amount = :amount WHERE id = :id');
        $stmt->execute([
            ':check_out_at' => $session->checkOutAt()?->format('c'),
            ':hours' => $hours,
            ':amount' => $amount,
            ':id' => $session->id(),
        ]);
        return $session;
    }

    public function listActive(): array
    {
        $rows = $this->pdo->query('SELECT * FROM parking_sessions WHERE check_out_at IS NULL ORDER BY check_in_at ASC')->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    public function listCompleted(): array
    {
        $rows = $this->pdo->query('SELECT * FROM parking_sessions WHERE check_out_at IS NOT NULL ORDER BY check_out_at DESC')->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    private function mapRow(array $row): ParkingSession
    {
        $vehicle = new Vehicle($row['plate'], VehicleType::from($row['type']));
        $in = new DateTimeImmutable($row['check_in_at']);
        $out = $row['check_out_at'] ? new DateTimeImmutable($row['check_out_at']) : null;
        $hours = isset($row['hours']) ? (int) $row['hours'] : null;
        $amount = isset($row['amount']) ? (float) $row['amount'] : null;
        return new ParkingSession((int) $row['id'], $vehicle, $in, $out, $hours, $amount);
    }
}