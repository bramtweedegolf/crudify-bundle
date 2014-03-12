<?php

namespace Bs\CrudifyBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bs_crudify');

        $rootNode
            ->children()
                ->scalarNode('default')->defaultNull()->end()
                ->scalarNode('controller')->defaultValue('bs_crudify.controller.base')->end()
                ->enumNode('default_access')
                    ->defaultValue('grant')
                    ->values(['grant', 'abstain', 'deny'])
                ->end()
                ->arrayNode('templates')
                    ->treatNullLike([])
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('layout')->defaultValue('BsCrudifyBundle:Admin:_layout.html.twig')->end()
                        ->scalarNode('form_theme')->defaultValue('BsBootstrapifyBundle::form.html.twig')->end()
                        ->scalarNode('pagination')
                            ->defaultValue('KnpPaginatorBundle:Pagination:twitter_bootstrap_v3_pagination.html.twig')
                        ->end()
                        ->scalarNode('sortable')
                            ->defaultValue('BsCrudifyBundle:Pagination:sortable_link.html.twig')
                        ->end()
                        ->scalarNode('index')->defaultValue('BsCrudifyBundle:Admin:index.html.twig')->end()
                        ->scalarNode('edit')->defaultValue('BsCrudifyBundle:Admin:edit.html.twig')->end()
                        ->scalarNode('new')->defaultValue('BsCrudifyBundle:Admin:new.html.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('mappings')
                    ->requiresAtLeastOneElement()
                    ->isRequired()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('entity')->end()
                            ->scalarNode('title')->defaultNull()->end()
                            ->arrayNode('index')
                                ->treatNullLike(['type' => 'dynamic'])
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->enumNode('type')
                                        ->values(['static', 'dynamic'])
                                        ->defaultValue('static')
                                    ->end()
                                    ->integerNode('page_limit')->defaultValue(20)->end()
                                    ->scalarNode('query_modifier')->defaultNull()->end()
                                    ->arrayNode('sort')
                                        ->treatNullLike([])
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('column')->defaultNull()->end()
                                            ->enumNode('direction')
                                                ->values(['asc', 'desc'])
                                                ->defaultValue('asc')
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('columns')
                                        ->isRequired()
                                        ->prototype('array')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(function ($v) { return ['title' => null, 'type' => $v]; })
                                            ->end()
                                            ->treatNullLike([])
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('title')->defaultNull()->end()
                                                ->scalarNode('type')->defaultValue('text')->end()
                                                ->scalarNode('path')->defaultNull()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('form')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function ($v) { return ['create' => $v, 'update' => $v]; })
                                ->end()
                                ->children()
                                    ->scalarNode('create')->end()
                                    ->scalarNode('update')->end()
                                ->end()
                            ->end()
                            ->scalarNode('form_options_provider')
                                ->defaultValue('bs_crudify.form.options.provider.basic')
                            ->end()
                            ->scalarNode('object_retriever')
                                ->defaultValue('bs_crudify.query.retriever.repository')
                            ->end()
                            ->booleanNode('create')->defaultTrue()->end()
                            ->booleanNode('update')->defaultTrue()->end()
                            ->booleanNode('delete')->defaultTrue()->end()
                            ->scalarNode('controller')->defaultNull()->end()
                            ->arrayNode('templates')
                                ->treatNullLike([])
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('index')->defaultNull()->end()
                                    ->scalarNode('edit')->defaultNull()->end()
                                    ->scalarNode('new')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}