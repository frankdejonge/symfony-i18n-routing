<?php

use FrankDeJonge\SymfonyI18nRouting\Routing\I18nRouter;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

$container->register('frankdejonge_i18n_routing.yaml_loader', YamlFileLoader::class)
    ->addTag('routing.loader')
    ->addArgument(new Reference('file_locator'));

$container->register('frankdejonge_i18n_routing.router', I18nRouter::class)
    ->addArgument(new Reference('router.default'))
    ->addArgument('%frankdejonge_i18n_routing.default_locale%');

$container->setAlias('router', 'frankdejonge_i18n_routing.router');