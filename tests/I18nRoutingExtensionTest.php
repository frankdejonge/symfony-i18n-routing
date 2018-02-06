<?php

use FrankDeJonge\SymfonyI18nRouting\I18nRoutingBundle;
use FrankDeJonge\SymfonyI18nRouting\Routing\I18nRouter;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\AnnotatedI18nRouteLoader;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;
use Symfony\Component\Routing\Router;

class I18nRoutingExtensionTest extends AbstractExtensionTestCase
{
    protected function getMinimalConfiguration()
    {
        return ['default_locale' => 'en'];
    }

    /**
     * @test
     */
    public function it_registers_a_router()
    {
        $this->load();
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.router');
    }

    /**
     * @test
     */
    public function loading_annotation_services()
    {
        $this->load(['use_annotations' => true]);
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.annotation.class_loader', AnnotatedI18nRouteLoader::class);
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.annotation.file_loader', AnnotationFileLoader::class);
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.annotation.directory_loader', AnnotationDirectoryLoader::class);
    }

    /**
     * Return an array of container extensions you need to be registered for each test (usually just the container
     * extension you are testing.
     *
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [(new I18nRoutingBundle())->getContainerExtension()];
    }
}