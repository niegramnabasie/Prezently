<?php

namespace App\Form;

use App\Entity\GiftCategory;
use App\Entity\GiftInQuestionnaire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftInQuestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Nazwa'
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'PLN',
                'label' => 'Cena',
            ])
            ->add('voteAmount')
            ->add('questionnaireId')
            ->add('category', EntityType::class, [
                'label'=>'Kategoria',
                'class' => GiftCategory::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GiftInQuestionnaire::class,
        ]);
    }
}
