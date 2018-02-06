<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"en": "/en", "nl": "/nl"})
 */
class LocalizedPrefixWithRouteWithoutLocale
{
    /**
     * @I18nRoute("/{param}", name="action")
     */
    public function action() {}
}