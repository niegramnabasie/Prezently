<?php

namespace App\Service;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Security\Core\Security;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class EventUtil
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;
    private $security;

    public function __construct(EventRepository $eventRepository, UserRepository $userRepository, Security $security)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function getEventsByUser ()
    {
        $user = $this->security->getUser();

        $events = [];
        $events['activ'] = $this->eventRepository->findActivByUser($user);
        $events['inactiv'] = $this->eventRepository->findInactivByUser($user);
        return $events;
    }

    public function getAmountOfGifts(Array $events) //eg. $events['activ']
    {

    }

}