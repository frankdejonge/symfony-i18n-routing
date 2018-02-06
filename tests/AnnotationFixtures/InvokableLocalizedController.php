<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"nl": "/hier", "en": "/here"}, name="action")
 */
class InvokableLocalizedController
{
    public function __invoke()
    {
    }
}