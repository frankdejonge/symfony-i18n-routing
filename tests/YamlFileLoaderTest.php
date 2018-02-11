<?php

include_once __DIR__.'/FileLocatorStub.php';

use FrankDeJonge\SymfonyI18nRouting\Routing\Loader\YamlFileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;

class YamlFileLoaderTest extends TestCase
{
    /**
     * @var FileLoader
     */
    private $locator;

    /**
     * @var YamlFileLoader
     */
    private $loader;

    /**
     * @before
     */
    public function setupLoader()
    {
        $this->locator = new FileLocator(__DIR__.'/fixtures/');
        $this->loader = new YamlFileLoader($this->locator);
    }

    /**
     * @before
     */
    public function setupStubbedLoader()
    {
        $this->locator = new FileLocatorStub();
        $this->loader = new YamlFileLoader($this->locator);
    }

    /**
     * @test
     */
    public function test_it_accepts_yaml_files()
    {
        $this->assertTrue($this->loader->supports('something.yaml', 'i18n_routes'));
        $this->assertTrue($this->loader->supports('something.yml', 'i18n_routes'));
        $this->assertFalse($this->loader->supports('something.yaml', 'routes'));
        $this->assertFalse($this->loader->supports('something.xml'));
    }

    /**
     * @test
     */
    public function loading_an_empty_file()
    {
        $routes = $this->loader->load('empty.yml');
        $this->assertEmpty($routes->all());
    }

    /**
     * @test
     */
    public function remote_sources_are_not_accepted()
    {
        $this->setupStubbedLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('http://remote.com/here.yml');
    }

    /**
     * @test
     */
    public function loading_non_existing_files()
    {
        $this->setupStubbedLoader();
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('non-existing.yml');
    }

    /**
     * @test
     */
    public function loading_invalid_yaml()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('invalid.yml');
    }

    /**
     * @test
     */
    public function loading_not_an_array()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('not-an-array.yml');
    }

    /**
     * @test
     */
    public function loading_a_localized_route()
    {
        $routes = $this->loader->load('localized-route.yml');

        $this->assertCount(3, $routes);
    }

    /**
     * @test
     */
    public function importing_routes_from_a_definition()
    {
        $routes = $this->loader->load('importing-localized-route.yml');

        $this->assertCount(3, $routes);
        $this->assertEquals('/nl', $routes->get('home.nl')->getPath());
        $this->assertEquals('/en', $routes->get('home.en')->getPath());
        $this->assertEquals('/here', $routes->get('not_localized')->getPath());
    }

    /**
     * @test
     */
    public function importing_routes_with_locales()
    {
        $routes = $this->loader->load('importer-with-locale.yml');

        $this->assertCount(2, $routes);
        $this->assertEquals('/nl/voorbeeld', $routes->get('imported.nl')->getPath());
        $this->assertEquals('/en/example', $routes->get('imported.en')->getPath());
    }

    /**
     * @test
     */
    public function importing_routes_from_a_definition_missing_a_locale_prefix()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('missing-locale-in-importer.yml');
    }

    /**
     * @test
     */
    public function importing_not_localized_routes_from_a_localized_import()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('importing-not-localized-with-localized-prefix.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_that_is_not_an_array()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-is-not-an-array.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_with_too_many_properties()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-has-too-many-properties.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_without_a_path_or_locales()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-without-path-or-locales.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_with_a_resource()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-with-resource.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_with_a_type()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-with-type.yml');
    }

    /**
     * @test
     */
    public function importing_a_route_without_a_controller()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load('route-with-2-controllers.yml');
    }

    /**
     * @test
     */
    public function importing_with_a_controller_default()
    {
        $routes = $this->loader->load('importer-with-controller-default.yml');
        $this->assertCount(3, $routes);
        $controller = $routes->get('home.en')->getDefault('_controller');
        $this->assertEquals('DefaultController::defaultAction', $controller);
    }

    /**
     * @test
     */
    public function importing_with_a_full_definition()
    {
        $routes = $this->loader->load('importer-with-all-options.yml');
        $this->assertCount(3, $routes);
        $route = $routes->get('home.en');

        $this->assertEquals(['POST', 'GET'], $route->getMethods());
        $this->assertEquals(['https', 'http'], $route->getSchemes());
        $this->assertEquals("context.getMethod() in ['GET', 'HEAD']", $route->getCondition());
    }
}