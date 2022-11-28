<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Repository\EventRepository;
use App\Repository\GiftRepository;
use App\Service\EventUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gift')]
class GiftController extends AbstractController
{
    #[Route('/', name: 'app_gift_index', methods: ['GET'])]
    public function index(GiftRepository $giftRepository): Response
    {
        return $this->render('gift/index.html.twig', [
            'gifts' => $giftRepository->findAll(),
        ]);
    }

    #[Route('/new/{eventId}', name: 'app_gift_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GiftRepository $giftRepository, EventRepository $eventRepository, $eventId, EventUtil $eventUtil): Response
    {
        $event = $eventRepository->find($eventId);
        if (!$event->isIsPaid() or !$eventUtil->isEventActive($event)){
            return $this->redirectToRoute('app_event_show', ['id'=> $eventId], Response::HTTP_SEE_OTHER);
        }
        if ($event->getAmountOfGifts()>=$event->getPricePoint()->getAmountOfGifts()){
            return $this->redirectToRoute('app_event_show', ['id'=> $eventId], Response::HTTP_SEE_OTHER);
        }

        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift);
        $form->get('event')->setData($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $giftRepository->save($gift, true);

            return $this->redirectToRoute('app_event_show', ['id'=> $eventId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gift/new.html.twig', [
            'gift' => $gift,
            'form' => $form,
            'event' => $event,
        ]);
    }

    #[Route('/{id}', name: 'app_gift_show', methods: ['GET'])]
    public function show(Gift $gift): Response
    {
        return $this->render('gift/show.html.twig', [
            'gift' => $gift,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gift_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gift $gift, GiftRepository $giftRepository): Response
    {
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        $event = $gift->getEvent();

        if ($form->isSubmitted() && $form->isValid()) {
            $giftRepository->save($gift, true);

            return $this->redirectToRoute('app_event_show', ['id'=> $event->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gift/edit.html.twig', [
            'gift' => $gift,
            'form' => $form,
            'event' => $event,
        ]);
    }

    #[Route('/{id}', name: 'app_gift_delete', methods: ['POST'])]
    public function delete(Request $request, Gift $gift, GiftRepository $giftRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gift->getId(), $request->request->get('_token'))) {
            $giftRepository->remove($gift, true);
        }

        return $this->redirectToRoute('app_gift_index', [], Response::HTTP_SEE_OTHER);
    }
}
