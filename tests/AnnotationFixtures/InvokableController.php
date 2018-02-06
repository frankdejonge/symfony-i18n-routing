<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute("/here", name="lol")
 */
class InvokableController
{
    public function __invoke()
    {
    }
}