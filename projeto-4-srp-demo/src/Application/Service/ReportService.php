<?php

namespace App\Application\Service;

use App\Application\Repository\SessionRepository;
use App\Domain\VehicleType;

final class ReportService
{
    private SessionRepository $sessions;

    public function __construct(SessionRepository $sessions)
    {
        $this->sessions = $sessions;
    }

    public function summaryByType(): array
    {
        $completed = $this->sessions->listCompleted();
        $result = [];
        foreach ([VehicleType::CAR, VehicleType::MOTORCYCLE, VehicleType::TRUCK] as $type) {
            $result[$type->value] = ['count' => 0, 'amount' => 0.0];
        }
        foreach ($completed as $session) {
            $key = $session->vehicle()->type()->value;
            $result[$key]['count'] += 1;
            $result[$key]['amount'] += (float) $session->amount();
        }
        return $result;
    }
}