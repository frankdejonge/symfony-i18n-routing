<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;

use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

class DefaultValueController
{
    /**
     * @I18nRoute("/path", name="action")
     */
    public function action($default = 'value') {}
}