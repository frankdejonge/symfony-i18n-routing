<?php

namespace FrankDeJonge\SymfonyI18nRouting\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class I18nRoutingExtension extends Extension
{
    public function getAlias()
    {
        return 'frankdejonge_i18n_routing';
    }

    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/')
        );

        $loader->load('services.php');
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('frankdejonge_i18n_routing.default_locale', $config['default_locale']);
        $this->configureAnnotationLoader($config, $loader);
    }

    private function configureAnnotationLoader(array $config, LoaderInterface $loader)
    {
        if ($config['use_annotations'] ?? false) {
            $loader->load('annotations.php');
        }
    }
}