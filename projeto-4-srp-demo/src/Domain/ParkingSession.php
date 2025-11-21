<?php

namespace App\Domain;

use DateTimeImmutable;

final class ParkingSession
{
    private ?int $id;
    private Vehicle $vehicle;
    private DateTimeImmutable $checkInAt;
    private ?DateTimeImmutable $checkOutAt;
    private ?int $hours;
    private ?float $amount;

    public function __construct(?int $id, Vehicle $vehicle, DateTimeImmutable $checkInAt, ?DateTimeImmutable $checkOutAt = null, ?int $hours = null, ?float $amount = null)
    {
        $this->id = $id;
        $this->vehicle = $vehicle;
        $this->checkInAt = $checkInAt;
        $this->checkOutAt = $checkOutAt;
        $this->hours = $hours;
        $this->amount = $amount;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function vehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function checkInAt(): DateTimeImmutable
    {
        return $this->checkInAt;
    }

    public function checkOutAt(): ?DateTimeImmutable
    {
        return $this->checkOutAt;
    }

    public function hours(): ?int
    {
        return $this->hours;
    }

    public function amount(): ?float
    {
        return $this->amount;
    }

    public function isActive(): bool
    {
        return $this->checkOutAt === null;
    }
}