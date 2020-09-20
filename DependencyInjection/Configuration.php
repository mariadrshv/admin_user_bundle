<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\DependencyInjection;

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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('appyfurious_admin_user');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()->variableNode('mailer_user')->end();

        return $treeBuilder;
    }
}
