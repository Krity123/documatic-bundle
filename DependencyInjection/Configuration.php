<?php

namespace Documatic\Bundle\DocumaticBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('documatic');

        $rootNode
            ->children()
                ->arrayNode('agreement')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('manager')
                            ->defaultValue('documatic.agreement.manager.propel')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('signature')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('manager')
                            ->defaultValue('documatic.signature.manager.propel')
                        ->end()
                        ->arrayNode('widget')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('form_theme')
                                    ->defaultValue('DocumaticBundle:Form:signature_bootstrap_3_layout.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
