<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Annotation;

use Symfony\Component\Routing\Annotation\Route;
use function is_array;

/**
 * I18nRoute annotation.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class I18nRoute extends Route
{
    private $locales = [];

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data[is_array($data['value']) ? 'locales' : 'path'] = $data['value'];
            unset($data['value']);
        }

        parent::__construct($data);
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales(array $locales)
    {
        $this->locales = $locales;
    }
}