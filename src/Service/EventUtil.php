<?php

namespace App\Service;

use App\Entity\Event;
use App\Form\EventType;
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
//        if(!empty($user)){
//            $userId = $user->getId();
//        }

        $events = [];
        $events['activ'] = $this->eventRepository->findActivByUser($user);
        $events['inactiv'] = $this->eventRepository->findInactivByUser($user);
        return $events;
    }

}