<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', options: [
                'label' => 'Titre'
            ])
            ->add('description')
            ->add('startDate', DateTimeType::class, [
                'widget' => 'choice',
                'input' => 'datetime',
                'years' => range(2023, 2030),
                'label' => 'Date de debut'
            ])
            ->add('endDate', DateTimeType::class, [
                'widget' => 'choice',
                'input' => 'datetime',
                'years' => range(2023, 2030),
                'label' => 'Date de fin'
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'divisor' => 100,
                'label' => 'Prix(facultatif)'
            ])
            ->add('imgFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
                'mapped' => false,
                'constraints' => [
                    new File(maxSize: '1024k', mimeTypes: ['image/jpg', 'image/jpeg', 'image/png']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
