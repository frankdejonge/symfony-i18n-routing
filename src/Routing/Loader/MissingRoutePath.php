<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Loader;

use LogicException;

class MissingRoutePath extends LogicException
{
    public static function forAnnotation(string $action): MissingRoutePath
    {
        return new MissingRoutePath("I18nRoute annotation for {$action} has no path.");
    }
}