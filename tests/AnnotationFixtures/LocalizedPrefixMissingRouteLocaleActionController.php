<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"nl": "/nl", "en": "/en"})
 */
class LocalizedPrefixMissingRouteLocaleActionController
{
    /**
     * @I18nRoute({"nl": "/actie"}, name="action")
     */
    public function action() {}
}