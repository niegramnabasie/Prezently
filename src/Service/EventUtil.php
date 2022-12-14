<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Gift;
use App\Entity\PricePoint;
use App\Form\EventType;
use App\Repository\DonationRepository;
use App\Repository\GiftInQuestionnaireRepository;
use App\Repository\GiftRepository;
use App\Repository\PricePointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class EventUtil
{
    private EventRepository $eventRepository;
    private PricePointRepository $pricePointRepository;
    private DonationRepository $donationRepository;
    private GiftRepository $giftRepository;
    private GiftInQuestionnaireRepository $giftInQuestionnaireRepository;
    private $security;

    public function __construct(EventRepository $eventRepository, PricePointRepository $pricePointRepository,
                                DonationRepository $donationRepository, GiftRepository $giftRepository,
                                GiftInQuestionnaireRepository $giftInQuestionnaireRepository, Security $security)
    {
        $this->eventRepository = $eventRepository;
        $this->pricePointRepository = $pricePointRepository;
        $this->donationRepository = $donationRepository;
        $this->giftRepository = $giftRepository;
        $this->giftInQuestionnaireRepository = $giftInQuestionnaireRepository;
        $this->security = $security;
    }

    public function getEventsByUser()
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

    public function setNewPricePoint(Event $event, PricePoint $pricePoint):void
    {
        $event->setPricePoint($pricePoint);
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

    public function addGiftsFromQuestionnaire (Event $event){
//        $giftInQuestionnaire = $event->getQuestionnaire()->getGiftsInQuestionnaire();
        $giftInQuestionnaire = $this->giftInQuestionnaireRepository->findAllOrderedByVotesAmount($event->getQuestionnaire());
        dump($giftInQuestionnaire);
        foreach ($giftInQuestionnaire as $gift) {
            $eventGift = new Gift();
            $eventGift->setName($gift->getName());
            $eventGift->setPrice($gift->getPrice());
            $eventGift->setEvent($gift->getQuestionnaireId()->getEventId());
            $eventGift->setCategory($gift->getCategory());

            $this->giftRepository->save($eventGift, true);
        }
    }

    public function checkEventsAfterQuestionnaire(array $events){
        foreach ($events['activ'] as $event) {
            if ($event->getQuestionnaire()){
                if(!$event->getAmountOfGifts()){        //if there is no gifts in event
                    $this->addGiftsFromQuestionnaire($event);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function generateSelector(){
        $generatedSelector = strtr(base64_encode(random_bytes(9)), '+/', '-_');
        if ($this->eventRepository->findOneBy(['selector' => $generatedSelector])){
            return $this->generateSelector();
        }
        return $generatedSelector;
    }
}