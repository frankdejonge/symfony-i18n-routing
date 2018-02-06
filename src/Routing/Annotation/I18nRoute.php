<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Annotation;

use InvalidArgumentException;
use function is_array;
use function ucfirst;

/**
 * I18nRoute annotation.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class I18nRoute
{
    private $path;

    private $locales = [];

    private $name;

    private $requirements = [];

    private $options = [];

    private $defaults = [];

    private $host;

    private $methods = [];

    private $schemes = [];

    private $condition;

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data[is_array($data['value']) ? 'locales' : 'path'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucfirst($key));

            if ( ! method_exists($this, $method)) {
                throw new InvalidArgumentException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setHost($pattern)
    {
        $this->host = $pattern;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setSchemes($schemes)
    {
        $this->schemes = is_array($schemes) ? $schemes : [$schemes];
    }

    public function getSchemes()
    {
        return $this->schemes;
    }

    public function setMethods($methods)
    {
        $this->methods = is_array($methods) ? $methods : [$methods];
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    public function getCondition()
    {
        return $this->condition;
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