<?php

namespace App\Domain\Pricing;

use App\Domain\ParkingSession;

final class CarPricingStrategy implements PricingStrategy
{
    public function calculate(ParkingSession $session, int $hours): float
    {
        return $hours * 5.0;
    }
}