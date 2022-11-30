<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Repository\EventRepository;
use App\Repository\PricePointRepository;
use App\Repository\QuestionnaireRepository;
use App\Service\EventUtil;
use App\Service\QuestionnaireUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/questionnaire')]
class QuestionnaireController extends AbstractController
{
    #[Route('/', name: 'app_questionnaire_index', methods: ['GET'])]
    public function index(QuestionnaireRepository $questionnaireRepository): Response
    {
        return $this->render('questionnaire/index.html.twig', [
            'questionnaires' => $questionnaireRepository->findAll(),
        ]);
    }

    #[Route('/new/{eventId}', name: 'app_questionnaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, $eventId, QuestionnaireRepository $questionnaireRepository, PricePointRepository $pricePointRepository, EventRepository $eventRepository, QuestionnaireUtil $questionnaireUtil): Response
    {
        $event = $eventRepository->find($eventId);
        if ($event->getPricePoint() !== $pricePointRepository->find(3)){
            return $this->redirectToRoute('app_event_show', ['id'=> $event->getId()], Response::HTTP_SEE_OTHER);
        }

        $questionnaire = new Questionnaire();

        $qEndDate = $questionnaireUtil->setQuestionnaireEndDate($event);
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->get('endDate')->setData($qEndDate);
        $form->get('eventId')->setData($event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaireRepository->save($questionnaire, true);

            return $this->redirectToRoute('app_questionnaire_show', ['id'=> $questionnaire->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('questionnaire/new.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form,
            'qEndDate'=>$qEndDate->format('d-m-Y'),
            'eventId' => $eventId
        ]);
    }

    #[Route('/{id}', name: 'app_questionnaire_show', methods: ['GET'])]
    public function show(Questionnaire $questionnaire, EventUtil $eventUtil): Response
    {
        //wyswietlanie prezentów i sama ankieta, przekierowanie do dodawania prezentów
        $event = $questionnaire->getEventId();
        $isUserACreator = $eventUtil->isUserACreator($event);

        return $this->render('questionnaire/show.html.twig', [
            'questionnaire' => $questionnaire,
            'isUserACreator'=>$isUserACreator,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_questionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Questionnaire $questionnaire, QuestionnaireRepository $questionnaireRepository): Response
    {
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaireRepository->save($questionnaire, true);

            return $this->redirectToRoute('app_questionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('questionnaire/edit.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_questionnaire_delete', methods: ['POST'])]
    public function delete(Request $request, Questionnaire $questionnaire, QuestionnaireRepository $questionnaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questionnaire->getId(), $request->request->get('_token'))) {
            $questionnaireRepository->remove($questionnaire, true);
        }

        return $this->redirectToRoute('app_questionnaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
