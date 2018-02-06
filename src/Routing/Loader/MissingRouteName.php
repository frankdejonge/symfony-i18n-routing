<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Loader;

use InvalidArgumentException;

class MissingRouteName extends InvalidArgumentException
{
    public static function forAnnotation(string $action): MissingRouteName
    {
        return new MissingRouteName("I18nRoute annotation for {$action} has no name.");
    }
}