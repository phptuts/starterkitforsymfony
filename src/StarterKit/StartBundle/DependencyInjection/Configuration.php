<?php

namespace StarterKit\StartBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('starter_kit_start');

        $rootNode->children()
            ->scalarNode('facebook_app_secret')->isRequired()->end()
            ->scalarNode('facebook_api_version')->isRequired()->end()
            ->scalarNode('facebook_app_id')->isRequired()->end()
            ->scalarNode('jws_pass_phrase')->isRequired()->end()
            ->scalarNode('google_client_id')->isRequired()->end()
            ->scalarNode('jws_ttl')->isRequired()->end()
            ->scalarNode('user_class')->isRequired()->end()
            ->scalarNode('refresh_token_ttl')->isRequired()->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
