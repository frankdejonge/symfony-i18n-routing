<?php

namespace FrankDeJonge\SymfonyI18nRouting\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ReplaceRouterCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('router', 'frankdejonge_i18n_routing.router')
            ->setPublic('true');
    }
}