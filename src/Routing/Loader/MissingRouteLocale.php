<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Loader;

use LogicException;
use ReflectionClass;
use ReflectionMethod;

class MissingRouteLocale extends LogicException
{
    public static function forClass(ReflectionClass $class, ReflectionMethod $method, string $locale): MissingRouteLocale
    {
        return new MissingRouteLocale("Localized prefix(es) {$locale} is/are not defined on {$class->name} while it is/are defined on its action {$method->name}.");
    }
}