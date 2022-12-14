<?php

namespace App\Form;

use App\Entity\GiftCategory;
use App\Entity\GiftInQuestionnaire;
use App\Entity\Questionnaire;
use App\Repository\EventRepository;
use App\Repository\GiftInQuestionnaireRepository;
use Doctrine\DBAL\Types\IntegerType;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftQuestionnaireForm extends AbstractType
{
    private $giftInQuestionnaireRepository;

    public function __construct(GiftInQuestionnaireRepository $giftInQuestionnaireRepository)
    {
        $this->giftInQuestionnaireRepository = $giftInQuestionnaireRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gifts',EntityType::class, [
                'class' => GiftInQuestionnaire::class,
                'choice_label' => 'name',
                'choices' => $this->giftInQuestionnaireRepository->findBy(['questionnaireId'=>$options['Questionnaire']]),
                'multiple' => true,
                'expanded'=>true,
                'label'=>false
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'Questionnaire' => Questionnaire::class,
        ]);
    }


}