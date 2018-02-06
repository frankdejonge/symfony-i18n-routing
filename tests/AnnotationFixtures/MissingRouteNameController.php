<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

class MissingRouteNameController
{
    /**
     * @I18nRoute("/path")
     */
    public function action() {}
}