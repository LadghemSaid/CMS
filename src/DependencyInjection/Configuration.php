<?php

namespace PiedWeb\CMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * php bin/console config:dump-reference PiedWebCMSBundle.
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('piedweb_cms');
        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('media_dir_absolute') // NOT USED ??
                        ->defaultValue('%kernel.project_dir%/media')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('locale')->defaultValue('%locale%')->cannotBeEmpty()->end()
                    ->scalarNode('locales')->defaultValue('fr|en')->end()
                    ->scalarNode('dir') // not explicit = public dir/web dir
                        ->defaultValue('%kernel.project_dir%/public')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('name')->defaultValue('PiedWeb.com')->end()
                    ->scalarNode('color')->defaultValue('#1fa67a')->end()
                    ->booleanNode('default_locale_without_prefix')->defaultTrue()->end()
                    ->scalarNode('default_page_template')
                        ->defaultValue('@PiedWebCMS/page/page.html.twig')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('entity_page')->defaultValue('App\Entity\Page')->cannotBeEmpty()->end()
                    ->scalarNode('entity_media')->defaultValue('App\Entity\Media')->cannotBeEmpty()->end()
                    ->scalarNode('entity_user')->defaultValue('App\Entity\User')->cannotBeEmpty()->end()
                    ->scalarNode('entity_pagehasmedia')
                        ->defaultValue('App\Entity\PageHasMedia')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('email_to_notify')->defaultValue('')->end()
                    // For Fos User and maybe other bundle
                    ->scalarNode('email_sender')->defaultValue('me@tld.com')->cannotBeEmpty()->end()
                    ->scalarNode('email_sender_name')->defaultValue('PiedWebCMS')->cannotBeEmpty()->end()
                    ->scalarNode('base_url')->defaultValue('')->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
