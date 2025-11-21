<?php

namespace App\Domain\Pricing;

use App\Domain\ParkingSession;

final class TruckPricingStrategy implements PricingStrategy
{
    public function calculate(ParkingSession $session, int $hours): float
    {
        return $hours * 10.0;
    }
}