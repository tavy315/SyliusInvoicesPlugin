<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerInvoiceProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_amount',
            ])
            ->add('description', TextType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_description',
            ])
            ->add('no', TextType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_no',
            ])
            ->add('price', NumberType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_price',
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_quantity',
            ])
            ->add('measurement', TextType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_measurement',
            ])
            ->add('type', TextType::class, [
                'label' => 'tavy315_sylius_invoices.ui.product_type',
            ]);
    }
}
