<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;
use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;

/**
 * @I18nRoute({"en": "/the/path", "nl": "/het/pad"})
 */
class LocalizedMethodActionControllers
{
    /**
     * @I18nRoute(name="post", methods={"POST"})
     */
    public function post() {}

    /**
     * @I18nRoute(name="put", methods={"PUT"})
     */
    public function put() {}
}