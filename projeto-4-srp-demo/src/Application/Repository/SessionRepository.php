<?php

namespace App\Application\Repository;

use App\Domain\ParkingSession;

interface SessionRepository
{
    public function create(ParkingSession $session): ParkingSession;
    public function findActiveByPlate(string $plate): ?ParkingSession;
    public function checkout(ParkingSession $session, int $hours, float $amount): ParkingSession;
    public function listActive(): array;
    public function listCompleted(): array;
}