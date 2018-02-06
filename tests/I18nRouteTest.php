<?php

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;
use PHPUnit\Framework\TestCase;

class I18nRouteTest extends TestCase
{
    /**
     * @test
     */
    public function passing_invalid_properties()
    {
        $this->expectException(InvalidArgumentException::class);
        new I18nRoute(['invalid' => 'property']);
    }

    /**
     * @test
     * @dataProvider validPropertiesProvider
     */
    public function passing_valid_properties($property, $value)
    {
        $route = new I18nRoute([$property => $value]);
        $this->assertEquals($value, $route->{"get" . ucfirst($property)}());
    }

    public function validPropertiesProvider()
    {
        return [
            ['path', 'value'],
            ['locales', ['nl', 'es']],
            ['name', 'value'],
            ['requirements', ['segment' => '.*']],
            ['options', ['option' => true]],
            ['defaults', ['default' => 'value']],
            ['host', 'localhost'],
            ['methods', ['POST', 'PUT']],
            ['schemes', ['ftp', 'https']],
            ['condition', 'something=valid'],
        ];
    }

    /**
     * @test
     * @dataProvider validValues
     */
    public function passing_valid_values($getter, $value)
    {
        $route = new I18nRoute(['value' => $value]);
        $this->assertEquals($value, $route->{$getter}());
    }

    public function validValues()
    {
        return [
            ['getPath', '/nl'],
            ['getLocales', ['nl' => '/nl']],
        ];
    }
}