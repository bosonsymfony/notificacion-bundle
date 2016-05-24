<?php

namespace UCI\Boson\NotificacionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('notificacion');

        $rootNode->children()
//            Responde al RF 99 Configurar línea temática
            ->scalarNode('url_server')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('store_attachments')->cannotBeEmpty()->end()
//            Responde al RF 100 Configurar framework de la capa de presentación
            ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
