<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute("/prefix")
 */
class PrefixedActionLocalizedRouteController
{
    /**
     * @I18nRoute({"en": "/path", "nl": "/pad"}, name="action")
     */
    public function action() {}
}