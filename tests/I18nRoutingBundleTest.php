<?php

use FrankDeJonge\SymfonyI18nRouting\I18nRoutingBundle;
use FrankDeJonge\SymfonyI18nRouting\Routing\I18nRouter;
use Nyholm\BundleTest\BaseBundleTestCase;

class I18nRoutingBundleTest extends BaseBundleTestCase
{
    /**
     * @return string
     */
    protected function getBundleClass()
    {
        return I18nRoutingBundle::class;
    }

    /**
     * @test
     */
    public function replacing_the_router()
    {
        $this->bootKernel();
        $container = $this->getContainer();
        $router = $container->get('router');
        $this->assertInstanceOf(I18nRouter::class, $router);
    } 
}