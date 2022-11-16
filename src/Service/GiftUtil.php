<?php

namespace App\Service;

use App\Repository\GiftRepository;

class GiftUtil
{
    private GiftRepository $giftRepository;

    public function __construct(GiftRepository $giftRepository)
    {
        $this->giftRepository = $giftRepository;

    }
}