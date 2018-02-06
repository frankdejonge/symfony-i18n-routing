<?php

namespace FrankDeJonge\SymfonyI18nRouting\AnnotationFixtures;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prefix")
 */
class SymfonyRouteWithPrefixController
{
    /**
     * @Route("/path", name="action")
     */
    public function action() {}
}