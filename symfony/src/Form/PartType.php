<?php

namespace App\Form;

use App\CarMaster\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

class PartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('namePart', null, [
            'label' => 'Name spare part',
            'attr' => [
                'placeholder' => 'Write name part',
                'pattern' => false,
                'required' => true
            ]

        ])
            ->add(
                'modelPart', null, [
                    'label' => 'Model spare part',
                    'attr' => [
                        'placeholder' => 'Write model',
                        'pattern' => false
                    ],
                    'required' => true
                ]
            )
            ->add(
                'pricePart',
                MoneyType::class,
                [
                    'label' => 'Price spare part in ',
                    'currency' => 'UAH',
                    'attr' => [
                        'placeholder' => 'Write price',
                    ],
                    'html5' => false,
                    'required' => true
                ]
            )
            ->add('vehicles', EntityType::class, [
                'class' => Vehicle::class,
                'label' => 'Choice vehicles',
                'choice_label' => 'brand',
                'placeholder' => 'Choose vehicles',
                'multiple' => true, // Для вибору декількох транспортних засобів
                'required' => true,
                'by_reference' => false]
            )
        ;
    }
    }