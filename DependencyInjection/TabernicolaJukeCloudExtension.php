<?php

namespace Tabernicola\JukeCloudBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TabernicolaJukeCloudExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('tabernicola_juke_cloud.config', $config);
        $container->setParameter('tabernicola_juke_cloud.plugins', $config['plugins']);
        
        //YOUTUBE PLUGIN
        $ytApiKey=null;
        if (isset($config['youtube_plugin']['apikey'])){
            $ytApiKey=$config['youtube_plugin']['apikey'];
        }
        $container->setParameter('tabernicola_juke_cloud.youtube_plugin.apikey', $ytApiKey);
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
    
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');
        // determine if AcmeGoodbyeBundle is registered
        if (!isset($bundles['LiipImagineBundle'])) {
            // disable AcmeGoodbyeBundle in bundles
            $config = array('use_covers' => false);
        }

        // process the configuration of AcmeHelloExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        // use the Configuration class to generate a config array with the settings "acme_hello"
        $config = $this->processConfiguration(new Configuration(), $configs);

        // check if entity_manager_name is set in the "acme_hello" configuration
        $rootdir=$container->getParameter('kernel.root_dir');
        //$datadir=$container->getParameter('tabernicola_juke_cloud.datadir');
        $liipImagine=array(
            'filter_sets'=>array(
                'navcover'=>array(
                    'filters'=>array(
                        'thumbnail'=>array('size'=> [16, 16], 'mode'=> 'inset')
                    )
                ),
                'plcover'=>array(
                    'filters'=>array(
                        'thumbnail'=>array('size'=> [32, 32], 'mode'=> 'inset')
                    )
                ),
                'infocover'=>array(
                    'filters'=>array(
                        'thumbnail'=>array('size'=> [90, 90], 'mode'=> 'inset')
                    )
                )
            ),
            'loaders'=>array('default'=>array('filesystem'=>array(
                'data_root'=> '%tabernicola_juke_cloud.covers_dir%'
            ))),
            'resolvers'=>array('default'=>array('web_path'=>array(
                'web_root'=> $rootdir.'/../web',
                'cache_prefix'=> 'thumbs'
            ))),
            'cache'=> 'default'
        );
        
        $liipTheme=array(
            'themes'=> array('web', 'tablet', 'phone'),
            'active_theme'=> 'web',
            'autodetect_theme'=>true,
            'load_controllers'=>false
        );
        
        // prepend the acme_something settings with the entity_manager_name
        $container->prependExtensionConfig("liip_imagine",  $liipImagine);
        $container->prependExtensionConfig("liip_theme",  $liipTheme);
    }
}
