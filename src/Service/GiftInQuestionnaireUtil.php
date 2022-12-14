<?php

namespace App\Service;

use App\Repository\GiftInQuestionnaireRepository;

class GiftInQuestionnaireUtil
{
    private GiftInQuestionnaireRepository $giftInQuestionnaireRepository;

    public function __construct(GiftInQuestionnaireRepository $giftInQuestionnaireRepository)
    {
        $this->giftInQuestionnaireRepository = $giftInQuestionnaireRepository;
    }

    public function addVotes($giftArray){
        dump($giftArray);
        foreach ($giftArray as $gift){
            dump($gift);
            $gift->addVote(1);
            $this->giftInQuestionnaireRepository->save($gift,true);
        }
    }

}