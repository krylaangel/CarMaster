<?php

namespace App\Form;

use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Entity\Enum\BodyTypes;
use App\CarMaster\Entity\SparePart;
use App\CarMaster\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

class VehicleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('licensePlate', null, [
            'label' => 'license plate',
            'attr' => [
                'placeholder' => 'Write license plate',
                'pattern' => false,
                'required' => true
            ]

        ])
            ->add('yearManufacture', null, [
                    'label' => 'year manufacture',
                    'attr' => [
                        'placeholder' => 'Write year manufacture',
                        'pattern' => false,
                        'required' => true
                    ]
                ]
            )
            ->add('brand', null, [
                    'label' => 'brand',
                    'attr' => [
                        'placeholder' => 'Write brand',
                        'pattern' => false,
                        'required' => true
                    ]
                ]
            )
//            ->add('spareParts', EntityType::class, [
//                    'class' => SparePart::class,
//                    'label' => 'Choice parts',
//                    'choice_label' => 'namePart',
//                    'placeholder' => 'Choose parts',
//                    'multiple' => true,
//                    'required' => true,
//                    'by_reference' => false
//                ]
//            )
            ->add('owner', EntityType::class, [
                'class' => CarOwner::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Choose owner',
            ]);
        if ($builder->getData() instanceof Car) {
            $builder->add('bodyTypes', EnumType::class, [
                'label' => 'body type',
                'class' => BodyTypes::class,
                'multiple' => true,
                'expanded' => false,
                'required' => true
            ]);
        }
    }
}