<?php

namespace App\Controller;

use App\Entity\Calendar;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateEventAction
{
    public function __invoke(Request $request, ManagerRegistry $doctrine): Calendar
    {
        $em = $doctrine->getManager();
        $json = json_decode($request->getContent(), true);
        $StartTime = new \DateTimeImmutable($json['added'][0]['StartTime']);
        $StartTime = $StartTime->add(new DateInterval("PT2H"));
        $EndTime = new \DateTimeImmutable($json['added'][0]['EndTime']);
        $EndTime = $EndTime->add(new DateInterval("PT2H"));

        $event = new Calendar();
        $event->setStartTime($StartTime->format("Y-m-d H:i:s"));
        $event->setEndTime($EndTime->format("Y-m-d H:i:s"));
        $event->setSubject($json["added"][0]["Subject"]);
        $event->setIsAllDay(false);
        $event->setIsBlock(false);
        $event->setIsReadOnly(false);

        $em->persist($event);
        $em->flush();

        return $event;
    }
}