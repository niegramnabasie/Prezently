<?php

namespace App\Entity;

use App\Repository\PricePointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PricePointRepository::class)]
class PricePoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $amountOfGifts = null;

    #[ORM\Column]
    private ?bool $qr = null;

    #[ORM\Column]
    private ?bool $questionnaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAmountOfGifts(): ?int
    {
        return $this->amountOfGifts;
    }

    public function setAmountOfGifts(int $amountOfGifts): self
    {
        $this->amountOfGifts = $amountOfGifts;

        return $this;
    }

    public function isQr(): ?bool
    {
        return $this->qr;
    }

    public function setQr(bool $qr): self
    {
        $this->qr = $qr;

        return $this;
    }

    public function isQuestionnaire(): ?bool
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(bool $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getName();
    }
}
