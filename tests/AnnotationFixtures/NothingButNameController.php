<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

class NothingButNameController
{
    /**
     * @I18nRoute(name="action")
     */
    public function action() {}
}