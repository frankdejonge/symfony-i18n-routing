<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"nl": "/nl"})
 */
class LocalizedPrefixMissingLocaleActionController
{
    /**
     * @I18nRoute({"nl": "/actie", "en": "/action"}, name="action")
     */
    public function action() {}
}