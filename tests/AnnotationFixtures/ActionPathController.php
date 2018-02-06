<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

class ActionPathController
{
    /**
     * @I18nRoute("/path", name="action")
     */
    public function action() {}
}