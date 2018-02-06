<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Loader;

use function array_diff;
use function array_keys;
use Doctrine\Common\Annotations\Reader;
use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;
use function join;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AnnotatedI18nRouteLoader extends AnnotationClassLoader
{
    protected $routeAnnotationClass = I18nRoute::class;

    public function __construct(Reader $reader)
    {
        parent::__construct($reader);
        $this->setRouteAnnotationClass(I18nRoute::class);
    }

    /**
     * @param RouteCollection  $collection
     * @param I18nRoute        $annotation
     * @param array            $globals
     * @param ReflectionClass  $class
     * @param ReflectionMethod $method
     */
    protected function addRoute(RouteCollection $collection, $annotation, $globals, ReflectionClass $class, ReflectionMethod $method)
    {
        $name = $annotation->getName();

        if (null === $name) {
            throw MissingRouteName::forAnnotation($class->name . '::' . $method->name);
        }

        $defaults = array_replace($globals['defaults'], $annotation->getDefaults());

        foreach ($method->getParameters() as $param) {
            if ( ! isset($defaults[$param->getName()]) && $param->isDefaultValueAvailable()) {
                $defaults[$param->getName()] = $param->getDefaultValue();
            }
        }

        $requirements = array_replace($globals['requirements'], $annotation->getRequirements());
        $options = array_replace($globals['options'], $annotation->getOptions());
        $schemes = array_merge($globals['schemes'], $annotation->getSchemes());
        $methods = array_merge($globals['methods'], $annotation->getMethods());
        $host = $annotation->getHost() ?: $globals['host'];
        $condition = $annotation->getCondition() ?: $globals['condition'];
        $path = $annotation->getPath();
        $locales = $annotation->getLocales();

        $hasLocalizedPrefix = empty($globals['locales']) === false;
        $hasPrefix = $hasLocalizedPrefix || empty($globals['path']) === false;
        $isLocalized = ! empty($locales);
        $hasPathOrLocales = empty($path) === false || $isLocalized;

        if ($hasPrefix === false && $hasPathOrLocales === false) {
            throw MissingRoutePath::forAnnotation("{$class->name}::{$method->name}");
        }

        if ( ! $hasPathOrLocales) {
            if ($hasLocalizedPrefix) {
                foreach ($globals['locales'] as $locale => $localePath) {
                    $routeName = "{$name}.{$locale}";
                    $route = $this->createRoute($localePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                    $this->configureRoute($route, $class, $method, $annotation);
                    $route->setDefault('_locale', $locale);
                    $collection->add($routeName, $route);
                }
            } else {
                $route = $this->createRoute($globals['path'], $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                $this->configureRoute($route, $class, $method, $annotation);
                $collection->add($name, $route);
            }
        } elseif ( ! $hasPrefix) {
            if ($isLocalized) {
                foreach ($locales as $locale => $localePath) {
                    $routeName = "{$name}.{$locale}";
                    $route = $this->createRoute($localePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                    $this->configureRoute($route, $class, $method, $annotation);
                    $route->setDefault('_locale', $locale);
                    $collection->add($routeName, $route);
                }
            } else {
                $route = $this->createRoute($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                $this->configureRoute($route, $class, $method, $annotation);
                $collection->add($name, $route);
            }
        } else {
            if ($hasLocalizedPrefix) {
                if ($isLocalized) {
                    $missing = array_diff(array_keys($globals['locales']), array_keys($locales));

                    if ( ! empty($missing)) {
                        throw MissingRouteLocale::forClass($class, $method, join(' and ', $missing));
                    }

                    foreach ($locales as $locale => $localePath) {
                        if ( ! isset($globals['locales'][$locale])) {
                            throw MissingRouteLocale::forClass($class, $method, $locale);
                        }

                        $routePath = $globals['locales'][$locale] . $localePath;
                        $routeName = "{$name}.{$locale}";
                        $route = $this->createRoute($routePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                        $this->configureRoute($route, $class, $method, $annotation);
                        $route->setDefault('_locale', $locale);
                        $collection->add($routeName, $route);
                    }
                } else {
                    foreach ($globals['locales'] as $locale => $localePrefix) {
                        $routeName = "{$name}.{$locale}";
                        $routePath = $localePrefix . $path;
                        $route = $this->createRoute($routePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                        $this->configureRoute($route, $class, $method, $annotation);
                        $route->setDefault('_locale', $locale);
                        $collection->add($routeName, $route);
                    }
                }
            } else {
                if ($isLocalized) {
                    foreach ($locales as $locale => $localePath) {
                        $routePath = $globals['path'] . $localePath;
                        $routeName = "{$name}.{$locale}";
                        $route = $this->createRoute($routePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                        $this->configureRoute($route, $class, $method, $annotation);
                        $route->setDefault('_locale', $locale);
                        $collection->add($routeName, $route);
                    }
                } else {
                    $routePath = $globals['path'] . $path;
                    $route = $this->createRoute($routePath, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
                    $this->configureRoute($route, $class, $method, $annotation);
                    $collection->add($name, $route);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function getGlobals(ReflectionClass $class)
    {
        $globals = [
            'path'         => '',
            'locales'      => [],
            'requirements' => [],
            'options'      => [],
            'defaults'     => [],
            'schemes'      => [],
            'methods'      => [],
            'host'         => '',
            'condition'    => '',
        ];

        $annotation = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass);

        if ( ! $annotation instanceof I18nRoute) {
            return $globals;
        }
        if (null !== $annotation->getLocales()) {
            $globals['locales'] = $annotation->getLocales();
        }
        if (null !== $annotation->getPath()) {
            $globals['path'] = $annotation->getPath();
        }
        if (null !== $annotation->getRequirements()) {
            $globals['requirements'] = $annotation->getRequirements();
        }
        if (null !== $annotation->getOptions()) {
            $globals['options'] = $annotation->getOptions();
        }
        if (null !== $annotation->getDefaults()) {
            $globals['defaults'] = $annotation->getDefaults();
        }
        if (null !== $annotation->getSchemes()) {
            $globals['schemes'] = $annotation->getSchemes();
        }
        if (null !== $annotation->getMethods()) {
            $globals['methods'] = $annotation->getMethods();
        }
        if (null !== $annotation->getHost()) {
            $globals['host'] = $annotation->getHost();
        }
        if (null !== $annotation->getCondition()) {
            $globals['condition'] = $annotation->getCondition();
        }

        return $globals;
    }

    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, $annot)
    {
        $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
    }
}