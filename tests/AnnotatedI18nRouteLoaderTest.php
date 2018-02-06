<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\AbstractClassController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\ActionPathController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\DefaultValueController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\InvokableController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\InvokableLocalizedController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedActionPathController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedMethodActionControllers;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedPrefixLocalizedActionController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedPrefixMissingLocaleActionController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedPrefixMissingRouteLocaleActionController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\LocalizedPrefixWithRouteWithoutLocale;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\MethodActionControllers;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\MissingRouteNameController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\NothingButNameController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\PrefixedActionLocalizedRouteController;
use FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures\PrefixedActionPathController;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\AnnotatedI18nRouteLoader;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\MissingRouteLocale;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\MissingRouteName;
use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\MissingRoutePath;
use PHPUnit\Framework\TestCase;

class AnnotatedI18nRouteLoaderTest extends TestCase
{
    /**
     * @var AnnotatedI18nRouteLoader
     */
    private $loader;

    /**
     * @before
     */
    public function register_annotation_loader()
    {
        $this->loader = new AnnotatedI18nRouteLoader(new AnnotationReader());
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @test
     */
    public function simple_path_routes()
    {
        $routes = $this->loader->load(ActionPathController::class);
        $this->assertCount(1, $routes);
        $this->assertEquals('/path', $routes->get('action')->getPath());
    }

    /**
     * @test
     */
    public function invokable_controller_loading()
    {
        $routes = $this->loader->load(InvokableController::class);
        $this->assertCount(1, $routes);
        $this->assertEquals('/here', $routes->get('lol')->getPath());
    }

    /**
     * @test
     */
    public function invokable_localized_controller_loading()
    {
        $routes = $this->loader->load(InvokableLocalizedController::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/here', $routes->get('action.en')->getPath());
        $this->assertEquals('/hier', $routes->get('action.nl')->getPath());
    }

    /**
     * @test
     */
    public function localized_path_routes()
    {
        $routes = $this->loader->load(LocalizedActionPathController::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/path', $routes->get('action.en')->getPath());
        $this->assertEquals('/pad', $routes->get('action.nl')->getPath());
    }

    /**
     * @test
     */
    public function default_values_for_methods()
    {
        $routes = $this->loader->load(DefaultValueController::class);
        $this->assertCount(1, $routes);
        $this->assertEquals('/path', $routes->get('action')->getPath());
        $this->assertEquals('value', $routes->get('action')->getDefault('default'));
    }

    /**
     * @test
     */
    public function method_action_controllers()
    {
        $routes = $this->loader->load(MethodActionControllers::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/the/path', $routes->get('put')->getPath());
        $this->assertEquals('/the/path', $routes->get('post')->getPath());
    }

    /**
     * @test
     */
    public function localized_method_action_controllers()
    {
        $routes = $this->loader->load(LocalizedMethodActionControllers::class);
        $this->assertCount(4, $routes);
        $this->assertEquals('/the/path', $routes->get('put.en')->getPath());
        $this->assertEquals('/the/path', $routes->get('post.en')->getPath());
    }

    /**
     * @test
     */
    public function route_with_path_with_prefix()
    {
        $routes = $this->loader->load(PrefixedActionPathController::class);
        $this->assertCount(1, $routes);
        $route = $routes->get('action');
        $this->assertEquals('/prefix/path', $route->getPath());
        $this->assertEquals('lol=fun', $route->getCondition());
        $this->assertEquals('frankdejonge.nl', $route->getHost());
    }

    /**
     * @test
     */
    public function localized_route_with_path_with_prefix()
    {
        $routes = $this->loader->load(PrefixedActionLocalizedRouteController::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/prefix/path', $routes->get('action.en')->getPath());
        $this->assertEquals('/prefix/pad', $routes->get('action.nl')->getPath());
    }

    /**
     * @test
     */
    public function localized_prefix_localized_route()
    {
        $routes = $this->loader->load(LocalizedPrefixLocalizedActionController::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/nl/actie', $routes->get('action.nl')->getPath());
        $this->assertEquals('/en/action', $routes->get('action.en')->getPath());
    }

    /**
     * @test
     */
    public function missing_a_prefix_locale()
    {
        $this->expectException(MissingRouteLocale::class);
        $this->loader->load(LocalizedPrefixMissingLocaleActionController::class);
    }

    /**
     * @test
     */
    public function missing_a_route_locale()
    {
        $this->expectException(MissingRouteLocale::class);
        $this->loader->load(LocalizedPrefixMissingRouteLocaleActionController::class);
    }

    /**
     * @test
     */
    public function missing_a_route_name()
    {
        $this->expectException(MissingRouteName::class);
        $this->loader->load(MissingRouteNameController::class);
    }

    /**
     * @test
     */
    public function nothing_but_a_name()
    {
        $this->expectException(MissingRoutePath::class);
        $this->loader->load(NothingButNameController::class);
    }

    /**
     * @test
     */
    public function non_existing_class_loading()
    {
        $this->expectException(LogicException::class);
        $this->loader->load('ClassThatDoesNotExist');
    }

    /**
     * @test
     */
    public function loading_an_abstract_class()
    {
        $this->expectException(LogicException::class);
        $this->loader->load(AbstractClassController::class);
    }

    /**
     * @test
     */
    public function localized_prefix_without_route_locale()
    {
        $routes = $this->loader->load(LocalizedPrefixWithRouteWithoutLocale::class);
        $this->assertCount(2, $routes);
        $this->assertEquals('/en/{param}', $routes->get('action.en')->getPath());
        $this->assertEquals('/nl/{param}', $routes->get('action.nl')->getPath());
    }
}