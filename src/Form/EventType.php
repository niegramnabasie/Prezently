<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Tytuł wydarzenia',
            ])
            ->add('description', TextType::class, [
                'label' => 'Opis wydarzenia',
            ])
            ->add('endDate',DateType::class, [
                'label' => 'Data zakończenia',
                'data'   => new \DateTime(),
                'years' => range(date('Y'), date('Y')+5),
                'format' => 'd M y',
                'attr'   => [
                    'min' => ( new \DateTime() )->format('d-m-Y'),
                    'class'=>'offset-3 col-6'
                ],
            ])
            ->add('user')
            ->add('pricePoint')
            ->add('isPaid')
            ->add('photo', FileType::class, [
                'label' => 'Zdjęcie',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Prosze załadować poprawny plik zdjęcia (.jpg lub .png)',
                    ])
                ],
            ])
            ->add('selector')
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
