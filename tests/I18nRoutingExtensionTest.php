<?php

use FrankDeJonge\SymfonyI18nRouting\I18nRoutingBundle;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
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
        $this->container->register('router', Router::class);
        $this->load();
        $this->assertContainerBuilderHasAlias('router', 'frankdejonge_i18n_routing.router');
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.router');
    }

    /**
     * @test
     */
    public function it_handles_router_aliases()
    {
        $this->container->register('another_router', Router::class);
        $this->container->setAlias('router', 'another_router');
        $this->load();

        $this->assertContainerBuilderHasAlias('router', 'frankdejonge_i18n_routing.router');
        $this->assertContainerBuilderHasService('frankdejonge_i18n_routing.router');
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