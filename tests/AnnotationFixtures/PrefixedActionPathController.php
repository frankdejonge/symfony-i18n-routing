<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute("/prefix", host="frankdejonge.nl", condition="lol=fun")
 */
class PrefixedActionPathController
{
    /**
     * @I18nRoute("/path", name="action")
     */
    public function action() {}
}