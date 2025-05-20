<?php
// src/Form/RentalType.php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Customer;
use App\Entity\Rental;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldClass = 'mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500';

        /** @var Rental|null $rental */
        $rental = $builder->getData();
        $isEdit = $rental && null !== $rental->getId();
        $now    = new \DateTimeImmutable();

        //
        // --- START DATE ---
        //
        $startAttrs      = ['class' => $fieldClass];
        $startDisabled   = false;
        if ($isEdit && $rental->getStartDate() < $now) {
            $startDisabled = true;
            $startAttrs['class'] .= ' bg-gray-100 text-gray-500 cursor-not-allowed';
        }
        $builder->add('startDate', DateTimeType::class, [
            'widget'   => 'single_text',
            'label'    => 'Start Date',
            'disabled' => $startDisabled,
            'attr'     => $startAttrs,
        ]);

        //
        // --- END DATE ---
        //
        $builder->add('endDate', DateTimeType::class, [
            'widget' => 'single_text',
            'label'  => 'End Date',
            'attr'   => ['class' => $fieldClass],
        ]);

        //
        // --- COST ---
        //
        $builder->add('cost', MoneyType::class, [
            'currency' => 'MAD',
            'label'    => 'Cost',
            'attr'     => ['class' => $fieldClass],
        ]);

        //
        // --- CUSTOMER (always disabled on edit) ---
        //
        $custAttrs    = ['class' => $fieldClass];
        $custDisabled = $isEdit;
        if ($custDisabled) {
            $custAttrs['class'] .= ' bg-gray-100 text-gray-500 cursor-not-allowed';
        }
        $builder->add('customer', EntityType::class, [
            'class'        => Customer::class,
            'choice_label' => 'fullName',
            'label'        => 'Customer',
            'disabled'     => $custDisabled,
            'attr'         => $custAttrs,
        ]);

        //
        // --- CAR ---
        //
        $builder->add('car', EntityType::class, [
            'class'        => Car::class,
            'choice_label' => fn(Car $c) => sprintf('%s %s (%d)', $c->getBrand(), $c->getModel(), $c->getYear()),
            'label'        => 'Car',
            'required'     => false,
            'attr'         => ['class' => $fieldClass],
        ]);

        //
        // --- unmapped payment fields & save button (unchanged) ---
        //
        $builder
            ->add('paymentAmount', MoneyType::class, [
                'currency' => 'USD',
                'label'    => 'Payment Amount',
                'mapped'   => false,
                'attr'     => ['class' => $fieldClass],
            ])
            ->add('paymentDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label'  => 'Payment Date',
                'mapped' => false,
                'attr'   => ['class' => $fieldClass],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Rental',
                'attr'  => ['class' => 'bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-2 rounded'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
