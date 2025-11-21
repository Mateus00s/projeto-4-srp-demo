<?php

namespace App\Domain\Pricing;

use App\Domain\ParkingSession;

interface PricingStrategy
{
    public function calculate(ParkingSession $session, int $hours): float;
}