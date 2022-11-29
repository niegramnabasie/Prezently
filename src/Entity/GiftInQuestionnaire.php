<?php

namespace App\Entity;

use App\Repository\GiftInQuestionnaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GiftInQuestionnaireRepository::class)]
class GiftInQuestionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'giftsInQuestionnaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Questionnaire $questionnaireId = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GiftCategory $category = null;

    #[ORM\Column]
    private ?int $voteAmount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionnaireId(): ?Questionnaire
    {
        return $this->questionnaireId;
    }

    public function setQuestionnaireId(?Questionnaire $questionnaireId): self
    {
        $this->questionnaireId = $questionnaireId;

        return $this;
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

    public function getCategory(): ?giftCategory
    {
        return $this->category;
    }

    public function setCategory(?giftCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getVoteAmount(): ?int
    {
        return $this->voteAmount;
    }

    public function setVoteAmount(int $voteAmount): self
    {
        $this->voteAmount = $voteAmount;

        return $this;
    }
}
