<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoice;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;
use Tavy315\SyliusInvoicesPlugin\Form\Type\CustomerInvoiceType;
use Tavy315\SyliusInvoicesPlugin\Repository\CustomerInvoiceRepository;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tavy315_sylius_invoices');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end();
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer_invoice')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CustomerInvoiceType::class)->cannotBeEmpty()->end()
                                        ->scalarNode('model')->defaultValue(CustomerInvoice::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CustomerInvoiceRepository::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
