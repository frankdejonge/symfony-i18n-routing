<?php

use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\AnnotatedI18nRouteLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;

$container->register('frankdejonge_i18n_routing.annotation.directory_loader', AnnotationDirectoryLoader::class)
    ->addTag('routing.loader')
    ->setArguments([
        new Reference('file_locator'),
        new Reference('frankdejonge_i18n_routing.annotation.class_loader')
    ]);

$container->register('frankdejonge_i18n_routing.annotation.file_loader', AnnotationFileLoader::class)
    ->addTag('routing.loader')
    ->setArguments([
        new Reference('file_locator'),
        new Reference('frankdejonge_i18n_routing.annotation.class_loader')
    ]);

$container->register('frankdejonge_i18n_routing.annotation.class_loader', AnnotatedI18nRouteLoader::class)
    ->addTag('routing.loader')
    ->setArguments([
        new Reference('annotation_reader')
    ]);