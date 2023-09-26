<?php

namespace Documatic\Bundle\DocumaticBundle\DependencyInjection;

use Documatic\Bundle\DocumaticBundle\EventListener\RegistrationCompletedListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DocumaticExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));

        $loader->load('security.yml');
        $loader->load('manager.yml');
        $loader->load('form.yml');
        $loader->load('listener.yml');
        $loader->load('twig.yml');

        $container->setAlias('documatic.agreement.manager', $config['agreement']['manager']);
        $container->setAlias('documatic.signature.manager', $config['signature']['manager']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (true === isset($bundles['DocumaticEditorBundle'])) {
            $this->configureDocumaticEditorBundle($container);
        }

        if (true === isset($bundles['TwigBundle'])) {
            $this->configureTwigBundle($container, $config['signature']['widget']);
        }

        if (true === isset($bundles['PropelBundle'])) {
            $this->configurePropelBundle($container);
        }

        if (true === isset($bundles['FOSUserBundle'])) {
            $container->register('documatic.fos.registration_completed.listener', RegistrationCompletedListener::class)
                ->addArgument(new Reference('documatic.signature.manager'))
                ->addTag('kernel.event_subscriber');
        }
    }

    protected function configureDocumaticEditorBundle(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'documatic_editor',
            array(
                'document' => array(
                    'manager' => 'documatic.agreement.manager',
                ),
            )
        );
    }

    protected function configurePropelBundle(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'propel',
            array(
                'build-properties' => array(
                    'propel.behavior.signature_entity.class' => 'Documatic\Bundle\DocumaticBundle\Propel\Behavior\SignatureEntityBehavior',
                ),
            )
        );
    }

    protected function configureTwigBundle(ContainerBuilder $container, array $config)
    {
        $container->prependExtensionConfig(
            'twig',
            array(
                'form_themes' => array(
                    $config['form_theme'],
                ),
            )
        );
    }
}
