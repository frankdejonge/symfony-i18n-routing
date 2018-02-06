<?php

namespace FrankDeJonge\SymfonyI18nRouting\Routing\Loader;

use Doctrine\Common\Annotations\Reader;
use FrankDeJonge\SymfonyI18nRouting\Routing\Annotation\I18nRoute;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Annotation\Route as SymfonyRoute;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use function array_diff;
use function array_keys;
use function join;

class AnnotatedI18nRouteLoader extends AnnotationClassLoader
{
    /**
     * Loads from annotations from a class.
     *
     * @param string      $class A class name
     * @param string|null $type  The resource type
     *
     * @return RouteCollection A RouteCollection instance
     *
     * @throws \InvalidArgumentException When route can't be parsed
     */
    public function load($class, $type = null)
    {
        if ( ! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $class = new \ReflectionClass($class);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        $globals = $this->getGlobals($class);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($class->getFileName()));

        foreach ($class->getMethods() as $method) {
            $this->defaultRouteIndex = 0;
            foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
                if ($annotation instanceof SymfonyRoute) {
                    $this->addRoute($collection, $annotation, $globals, $class, $method);
                }
            }
        }

        if (0 === $collection->count() && $class->hasMethod('__invoke') && $annotation = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass)) {
            $globals['path'] = '';
            $globals['name'] = '';
            $globals['locales'] = [];
            $this->addRoute($collection, $annotation, $globals, $class, $class->getMethod('__invoke'));
        }

        return $collection;
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
            if ( ! isset($defaults[$param->name]) && $param->isDefaultValueAvailable()) {
                $defaults[$param->name] = $param->getDefaultValue();
            }
        }

        $requirements = array_replace($globals['requirements'], $annotation->getRequirements());
        $options = array_replace($globals['options'], $annotation->getOptions());
        $schemes = array_merge($globals['schemes'], $annotation->getSchemes());
        $methods = array_merge($globals['methods'], $annotation->getMethods());
        $host = $annotation->getHost() ?: $globals['host'];
        $condition = $annotation->getCondition() ?: $globals['condition'];
        $path = $annotation->getPath();
        $locales = $annotation instanceof I18nRoute ? $annotation->getLocales() : [];

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

        if ($annotation instanceof SymfonyRoute === false) {
            return $globals;
        }
        if ($annotation instanceof I18nRoute) {
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
        $route->setDefault('_controller', $class->name . '::' . $method->getName());
    }
}
