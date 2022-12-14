<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionnaireRepository::class)]
class Questionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

//    #[ORM\OneToOne(inversedBy: 'questionnaire', cascade: ['persist', 'remove'])]
    #[ORM\OneToOne(inversedBy: 'questionnaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $eventId = null;

    #[ORM\OneToMany(mappedBy: 'questionnaireId', targetEntity: GiftInQuestionnaire::class, orphanRemoval: true)]
    private Collection $giftsInQuestionnaire;

    #[ORM\Column]
    private ?int $endGiftAmount = null;

    public function __construct()
    {
        $this->giftsInQuestionnaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEventId(): ?Event
    {
        return $this->eventId;
    }

    public function setEventId(Event $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return Collection<int, GiftInQuestionnaire>
     */
    public function getGiftsInQuestionnaire(): Collection
    {
        return $this->giftsInQuestionnaire;
    }

    public function addGiftsInQuestionnaire(GiftInQuestionnaire $giftsInQuestionnaire): self
    {
        if (!$this->giftsInQuestionnaire->contains($giftsInQuestionnaire)) {
            $this->giftsInQuestionnaire->add($giftsInQuestionnaire);
            $giftsInQuestionnaire->setQuestionnaireId($this);
        }

        return $this;
    }

    public function removeGiftsInQuestionnaire(GiftInQuestionnaire $giftsInQuestionnaire): self
    {
        if ($this->giftsInQuestionnaire->removeElement($giftsInQuestionnaire)) {
            // set the owning side to null (unless already changed)
            if ($giftsInQuestionnaire->getQuestionnaireId() === $this) {
                $giftsInQuestionnaire->setQuestionnaireId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        $name = 'Ankieta wydarzenia '.$this->getEventId()->getName();
        return $name;
    }

    public function getEndGiftAmount(): ?int
    {
        return $this->endGiftAmount;
    }

    public function setEndGiftAmount(int $endGiftAmount): self
    {
        $this->endGiftAmount = $endGiftAmount;

        return $this;
    }
}
