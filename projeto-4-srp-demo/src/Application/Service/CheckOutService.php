<?php

namespace App\Application\Service;

use App\Application\Repository\SessionRepository;
use App\Domain\ParkingSession;
use DateTimeImmutable;

final class CheckOutService
{
    private SessionRepository $sessions;
    private PricingService $pricing;

    public function __construct(SessionRepository $sessions, PricingService $pricing)
    {
        $this->sessions = $sessions;
        $this->pricing = $pricing;
    }

    public function execute(ParkingSession $session): ParkingSession
    {
        $end = new DateTimeImmutable('now');
        $diff = $end->getTimestamp() - $session->checkInAt()->getTimestamp();
        $hours = (int) ceil($diff / 3600);
        $amount = $this->pricing->calculate($session, $hours);
        return $this->sessions->checkout(new ParkingSession($session->id(), $session->vehicle(), $session->checkInAt(), $end, $hours, $amount), $hours, $amount);
    }
}