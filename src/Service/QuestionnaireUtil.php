<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\DonationRepository;
use App\Repository\EventRepository;
use App\Repository\PricePointRepository;
use Symfony\Component\Security\Core\Security;

class QuestionnaireUtil
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

    public function setQuestionnaireEndDate(Event $event)
    {
        $eventEndDate = $event->getEndDate()->format('U');
        $today = (new \DateTime())->format('U');
        $timeDifference = $eventEndDate - $today;
        if ($timeDifference < (86400*5)){
            return new \DateTime('now +1 day');
        }
        elseif ($timeDifference < (86400*10)){
            return new \DateTime('now +2 day');
        }
        elseif ($timeDifference < (86400*15)){
            return new \DateTime('now +3 day');
        }
        else{
            return new \DateTime('now +5 day');
        }
    }



}