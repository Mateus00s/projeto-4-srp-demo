<?php

namespace App\Application\Service;

use App\Domain\VehicleType;
use App\Domain\ParkingSession;
use App\Domain\Pricing\PricingStrategy;
use App\Domain\Pricing\CarPricingStrategy;
use App\Domain\Pricing\MotorcyclePricingStrategy;
use App\Domain\Pricing\TruckPricingStrategy;

final class PricingService
{
    private array $strategies;

    public function __construct()
    {
        $this->strategies = [
            VehicleType::CAR->value => new CarPricingStrategy(),
            VehicleType::MOTORCYCLE->value => new MotorcyclePricingStrategy(),
            VehicleType::TRUCK->value => new TruckPricingStrategy(),
        ];
    }

    public function calculate(ParkingSession $session, int $hours): float
    {
        $type = $session->vehicle()->type()->value;
        $strategy = $this->strategies[$type] ?? null;
        if (!$strategy instanceof PricingStrategy) {
            throw new \RuntimeException('Tipo de veículo não suportado');
        }
        return $strategy->calculate($session, $hours);
    }
}