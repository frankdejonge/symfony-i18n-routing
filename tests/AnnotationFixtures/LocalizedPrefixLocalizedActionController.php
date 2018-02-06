<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"nl": "/nl", "en": "/en"})
 */
class LocalizedPrefixLocalizedActionController
{
    /**
     * @I18nRoute({"nl": "/actie", "en": "/action"}, name="action")
     */
    public function action() {}
}