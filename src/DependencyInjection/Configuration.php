<?php

namespace FrankDeJonge\SymfonyI18nRouting\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('frankdejonge_i18n_routing');
        $root->children()
            ->scalarNode('default_locale')
                ->defaultValue('en')
            ->end()
            ->booleanNode('use_annotations')
                ->defaultFalse()
            ->end()
        ->end();

        return $treeBuilder;
    }
}