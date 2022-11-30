<?php

namespace App\Controller;

use App\Entity\GiftInQuestionnaire;
use App\Form\GiftInQuestionnaireType;
use App\Repository\GiftInQuestionnaireRepository;
use App\Repository\QuestionnaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gift_in_questionnaire')]
class GiftInQuestionnaireController extends AbstractController
{
    #[Route('/', name: 'app_gift_in_questionnaire_index', methods: ['GET'])]
    public function index(GiftInQuestionnaireRepository $giftInQuestionnaireRepository): Response
    {
        return $this->render('gift_in_questionnaire/index.html.twig', [
            'gift_in_questionnaires' => $giftInQuestionnaireRepository->findAll(),
        ]);
    }

    #[Route('/new/{questionnaireId}', name: 'app_gift_in_questionnaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, $questionnaireId, GiftInQuestionnaireRepository $giftInQuestionnaireRepository, QuestionnaireRepository $questionnaireRepository): Response
    {
        $questionnaire = $questionnaireRepository->find($questionnaireId);

        $giftInQuestionnaire = new GiftInQuestionnaire();
        $form = $this->createForm(GiftInQuestionnaireType::class, $giftInQuestionnaire);
        $form->get('questionnaireId')->setData($questionnaire);
        $form->get('voteAmount')->setData(0);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $giftInQuestionnaireRepository->save($giftInQuestionnaire, true);

            return $this->redirectToRoute('app_questionnaire_show', ['id'=>$questionnaireId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gift_in_questionnaire/new.html.twig', [
            'gift_in_questionnaire' => $giftInQuestionnaire,
            'questionnaire'=>$questionnaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gift_in_questionnaire_show', methods: ['GET'])]
    public function show(GiftInQuestionnaire $giftInQuestionnaire): Response
    {
        return $this->render('gift_in_questionnaire/show.html.twig', [
            'gift_in_questionnaire' => $giftInQuestionnaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gift_in_questionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GiftInQuestionnaire $giftInQuestionnaire, GiftInQuestionnaireRepository $giftInQuestionnaireRepository): Response
    {
        $form = $this->createForm(GiftInQuestionnaireType::class, $giftInQuestionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $giftInQuestionnaireRepository->save($giftInQuestionnaire, true);

            return $this->redirectToRoute('app_gift_in_questionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gift_in_questionnaire/edit.html.twig', [
            'gift_in_questionnaire' => $giftInQuestionnaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gift_in_questionnaire_delete', methods: ['POST'])]
    public function delete(Request $request, GiftInQuestionnaire $giftInQuestionnaire, GiftInQuestionnaireRepository $giftInQuestionnaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$giftInQuestionnaire->getId(), $request->request->get('_token'))) {
            $giftInQuestionnaireRepository->remove($giftInQuestionnaire, true);
        }

        return $this->redirectToRoute('app_gift_in_questionnaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
