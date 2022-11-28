<?php

namespace App\Service;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\DonationRepository;
use App\Repository\PricePointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Security\Core\Security;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class EventUtil
{
    private EventRepository $eventRepository;
    private PricePointRepository $pricePointRepository;
    private DonationRepository $donationRepository;
    private $security;

    public function __construct(EventRepository $eventRepository, PricePointRepository $pricePointRepository, DonationRepository $donationRepository, Security $security)
    {
        $this->eventRepository = $eventRepository;
        $this->pricePointRepository = $pricePointRepository;
        $this->donationRepository = $donationRepository;
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

    public function nextPricePoint(Event $event)
    {
        $thisEventPP= $event->getPricePoint();
        $thisEventPPId = $thisEventPP->getId();
        if ($thisEventPPId > 2){
            $availableNext = false;
            $nextPricePoint = $thisEventPP;
        } else{
            $availableNext = true;
            $nextPricePoint = $this->pricePointRepository->find($thisEventPPId+1);
        }

        return [$nextPricePoint, $availableNext] ;
    }

    public function isUserACreator (Event $event)
    {
        $user = $this->security->getUser();
        $isUserACreator = false;

        if ($event->getUser() == $user){
            $isUserACreator = true;
        }
        return $isUserACreator;
    }

    public function getDonationsForEvent(Event $event)
    {
        $gifts = $event->getGifts();
        return $this->donationRepository->findAllDonationsForEvents($gifts);
    }

    public function setIsPaid(Event $event, bool $isPaid):void
    {
        $event->setIsPaid($isPaid);
        $this->eventRepository->save($event, true);
    }

    public function isEventActive(Event $event){
        $today = ( new \DateTime() )->format('Y-m-d');
        if ($event->getEndDate()->format('Y-m-d') < $today){
            return false;
        }
        elseif ($event->getEndDate()->format('Y-m-d') >= $today){
            return true;
        }
    }
}