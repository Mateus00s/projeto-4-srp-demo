<?php

namespace App\Domain\Pricing;

use App\Domain\ParkingSession;

final class MotorcyclePricingStrategy implements PricingStrategy
{
    public function calculate(ParkingSession $session, int $hours): float
    {
        return $hours * 3.0;
    }
}