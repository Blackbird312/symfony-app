<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldClass = 'mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500';

        $builder
            ->add('brand', TextType::class, [
                'label' => 'Brand',
                'attr'  => ['class' => $fieldClass],
            ])
            ->add('model', TextType::class, [
                'label' => 'Model',
                'attr'  => ['class' => $fieldClass],
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Year',
                'attr'  => ['class' => $fieldClass],
            ])
            ->add('carLicense', TextType::class, [
                'label' => 'License Plate',
                'attr'  => ['class' => $fieldClass],
            ])
            ->add('available', CheckboxType::class, [
                'label'    => 'Available',
                'required' => false,
                'attr'     => [
                    'class' => 'h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Changes',
                'attr'  => [
                    'class' => 'bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-2 rounded'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
