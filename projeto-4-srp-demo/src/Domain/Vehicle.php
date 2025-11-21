<?php

namespace App\Domain;

final class Vehicle
{
    private string $plate;
    private VehicleType $type;

    public function __construct(string $plate, VehicleType $type)
    {
        $this->plate = strtoupper(trim($plate));
        $this->type = $type;
    }

    public function plate(): string
    {
        return $this->plate;
    }

    public function type(): VehicleType
    {
        return $this->type;
    }
}