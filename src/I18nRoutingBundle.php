<?php

namespace FrankDeJonge\SymfonyI18nRouting;

use FrankDeJonge\SymfonyI18nRouting\DependencyInjection\I18nRoutingExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class I18nRoutingBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new I18nRoutingExtension();
    }
}