<?php

namespace App\Application\Service;

use App\Application\Repository\SessionRepository;
use App\Domain\ParkingSession;
use App\Domain\Vehicle;
use DateTimeImmutable;

final class CheckInService
{
    private SessionRepository $sessions;

    public function __construct(SessionRepository $sessions)
    {
        $this->sessions = $sessions;
    }

    public function execute(Vehicle $vehicle): ParkingSession
    {
        $existing = $this->sessions->findActiveByPlate($vehicle->plate());
        if ($existing) {
            throw new \RuntimeException('Veículo já está ativo');
        }
        $session = new ParkingSession(null, $vehicle, new DateTimeImmutable('now'));
        return $this->sessions->create($session);
    }
}