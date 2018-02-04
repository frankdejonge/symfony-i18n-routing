<?php

include_once __DIR__.'/RouterStub.php';

use FrankDeJonge\SymfonyI18nRouting\Routing\I18nRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class I18nRoutingRouterTest extends TestCase
{
    /**
     * @test
     */
    public function call_proxies()
    {
        $routes = new RouteCollection();
        $routes->add('home', new Route('/'));
        $internalRouter = new RouterStub($routes);
        $this->assertFalse($internalRouter->warmed);
        $router = new I18nRouter($internalRouter, 'en');

        $this->assertEquals($routes, $router->getRouteCollection());

        $router->warmUp('string');
        $this->assertTrue($internalRouter->warmed);

        $newContext = new RequestContext('/new-context/');
        $this->assertNull($router->getContext());
        $router->setContext($newContext);
        $this->assertEquals($newContext, $router->getContext());

        $match = $router->match('/');
        $this->assertEquals(['_route' => 'home'], $match);

        $this->assertEquals('http://not_i18n/', $router->generate('not_i18n'));
        $this->assertEquals('http://is_i18n.en/', $router->generate('is_i18n'));
    }
}