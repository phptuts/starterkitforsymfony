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
            ->scalarNode('jws_pass_phrase')->defaultValue('bad_pass_phrase')->end()
            ->integerNode('jws_ttl')->defaultValue(5184000)->end()
            ->integerNode('refresh_token_ttl')->defaultValue(10368000)->end()
            ->scalarNode('user_class')->defaultValue('AppBundle\Entity\User')->end()

            // Required for facebook and google login
            ->scalarNode('facebook_app_secret')->defaultNull()->end()
            ->scalarNode('facebook_api_version')->defaultNull()->end()
            ->scalarNode('facebook_app_id')->defaultNull()->end()
            ->scalarNode('google_client_id')->defaultNull()->end()

            // Required for s3 autoload
            ->scalarNode('aws_region')->defaultNull()->end()
            ->scalarNode('aws_key')->defaultNull()->end()
            ->scalarNode('aws_secret')->defaultNull()->end()
            ->scalarNode('aws_s3_bucket_name')->defaultNull()->end()
            ->scalarNode('aws_api_version')->defaultNull()->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
