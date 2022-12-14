<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\PricePointRepository;
use App\Service\EventUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Nzo\UrlEncryptorBundle\Annotations\ParamEncryptor;

#[Route('/event')]
class EventController extends AbstractController
{
    private $security;

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository, EventUtil $util): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $events = $util->getEventsByUser();
        $util->checkEventsAfterQuestionnaire($events);
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/new/', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository, PricePointRepository $pricePointRepository, EventUtil $util, SluggerInterface $slugger, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $this->security = $security;
        $user = $this->security->getUser();

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->get('user')->setData($user);
        $form->get('isPaid')->setData(false);
        $form->get('selector')->setData($util->generateSelector());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('event_photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $event->setPhoto($newFilename);
            }

            $eventRepository->save($event, true);

            $pricePoint = $form->get('pricePoint')->getData();
            if ($pricePoint == $pricePointRepository->find(2) or $pricePoint == $pricePointRepository->find(3)){
                return $this->redirectToRoute('app_event_payment', ['id'=> $event->getId()], Response::HTTP_SEE_OTHER);
            }
            elseif ($pricePoint == $pricePointRepository->find(1))
            {             //jeżeli darmowy pakiet to ustawiam ze opłacone
                $util->setIsPaid($event, true);
            }

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}', name: 'app_event_show', requirements: ['id' => '\d+'], methods: ['GET'])]

    #[Route('/{selector}', name: 'app_event_show', methods: ['GET'])]
    #[Entity("Event", expr: "repository.findBy(['selector' => 'selector'])")]
    public function show(Event $event, EventUtil $util): Response
    {
//      Check if the event has a Questionnaire and is it still active
        if ($event->getQuestionnaire()){
            if ($event->getQuestionnaire()->getEndDate()->format('U') > (new \DateTime())->format('U')){
                return $this->redirectToRoute('app_questionnaire_show', ['id'=>$event->getQuestionnaire()->getId()], Response::HTTP_SEE_OTHER);
            }
            else{
                if(!$event->getAmountOfGifts()){        //if there is no gifts in event
                    $util->addGiftsFromQuestionnaire($event);
                }
            }
        }

        $nextPP = $util->nextPricePoint($event);
        $isUserACreator = $util->isUserACreator($event);
        $donationsForEvent = $util->getDonationsForEvent($event);
        $eventStatus = $util->isEventActive($event);
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'nextPP' => $nextPP[0],
            'availableNext' => $nextPP[1],
            'isUserACreator' => $isUserACreator,
            'allDonations' => $donationsForEvent,
            'eventStatus' => $eventStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository, EventUtil $util): Response
    {
//      security options
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$util->isUserACreator($event)){
            return $this->redirectToRoute('app_event_show', ['selector'=> $event->getSelector()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_show', ['selector'=> $event->getSelector()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/payment', name: 'app_event_payment', methods: ['GET','POST'])]
    public function payment(Request $request, Event $event, EventRepository $eventRepository, EventUtil $util, Security $security, PricePointRepository $pricePointRepository): Response
    {
        if ($event->isIsPaid() or !$util->isUserACreator($event)){
            return $this->redirectToRoute('app_event_show', ['selector'=> $event->getSelector()], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createFormBuilder()
            ->add('price', NumberType::class,[
                'label'=>'Cena',
                'attr'=>[
                    'value'=>$event->getPricePoint()->getPrice(),
                ],
                'disabled'=>true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $util->setIsPaid($event,true);

            if ($event->getPricePoint() === $pricePointRepository->find(3)){

                return $this->redirectToRoute('app_questionnaire_new', ['eventId'=> $event->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_event_show', ['selector'=> $event->getSelector()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/payment.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/change_price_point', name: 'app_event_change_price_point', methods: ['GET','POST'])]
    public function change_price_point(Request $request, Event $event, EventUtil $util):Response
    {
        $nextPP = $util->nextPricePoint($event);
        $price = $nextPP[0]->getPrice() - $event->getPricePoint()->getPrice();
        $form = $this->createFormBuilder()
            ->add('price', NumberType::class,[
                'label'=>'Cena',
                'attr'=>[
                    'value'=>$price,
                ],
                'disabled'=>true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $util->setNewPricePoint($event, $nextPP[0]);

            $this->addFlash('success', 'Zmiana pakietu dla wydarzenia '.$event->getName().' przebiegła pomyślnie :)');

            return $this->redirectToRoute('app_event_show', ['selector'=> $event->getSelector()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/change_price_point.html.twig', [
            'nextPP' => $nextPP[0],
            'event' => $event,
            'form' => $form,
        ]);

    }
}
