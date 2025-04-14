<?php

namespace App\Form;

use App\Entity\Order;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'First name',
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Last name',
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adress',
            ])
            ->add('zip_code', TextType::class, [
                'label' => 'Zip code',
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Checkout',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
