<?php

namespace FrankDeJonge\SymfonyI18nRouting;

use FrankDeJonge\SymfonyI18nRouting\DependencyInjection\CompilerPass\ReplaceRouterCompilerPass;
use FrankDeJonge\SymfonyI18nRouting\DependencyInjection\I18nRoutingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class I18nRoutingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ReplaceRouterCompilerPass());
    }


    public function getContainerExtension()
    {
        return new I18nRoutingExtension();
    }
}