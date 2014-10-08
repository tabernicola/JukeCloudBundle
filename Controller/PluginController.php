<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tabernicola\JukeCloudBundle\Plugin\PluginInterface;
use Tabernicola\JukeCloudBundle\Plugin\AbstractInfoPlugin;

class PluginController extends Controller
{
    private $plugins;
    
    private function getPlugins($types=null){
        if(!$this->plugins){
            $this->plugins = $this->container->getParameter('tabernicola_juke_cloud.plugins');
        }
        
        $response=array();
        if($types){
            foreach ($types as $type){
                $response=  array_merge($response, $this->plugins[$type]);
            }
        }
        else{
            foreach ($this->plugins as $plugins){
                $response=  array_merge($response, $plugins);
            }
        }
        return $response;
    }
    
    private function getPluginByServiceSufix($serviceSufix){
        $plugins=$this->getPlugins();
            
        foreach($plugins as $serviceName){
            //Avoid the notice in the end function with the @
            if (@end(explode('.', $serviceName))===$serviceSufix){
                return $this->get($serviceName);
            }
        }
        return false;
        
    }
    
    public function javascriptAction()
    {
        $plugins = $this->getPlugins();
        $files=array();
        foreach($plugins as $plugin){
            /* @var $service PluginInterface */
            $service=$this->get($plugin);
            $files=array_merge($files,$service->getJavascriptFiles());
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:js.html.twig',array('files'=>$files));
    }
    
    public function cssAction()
    {
        $plugins = $this->getPlugins();
        $files=array();
        foreach($plugins as $plugin){
            /* @var $service PluginInterface */
            $service=$this->get($plugin);
            $files=array_merge($files,$service->getCssFiles());
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:css.html.twig',array('files'=>$files));
    }
    
    public function renderPluginBrowserAction()
    {
        $plugins = $this->getPlugins(array('info'));
        $services=array();
        foreach($plugins as $plugin){
            $services[]=$this->get($plugin);
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:infoTemplate.html.twig',array('plugins'=>$services));
    }
    
    public function renderPluginPlaylistAction()
    {
        $plugins = $this->getPlugins(array('playlist'));
        $services=array();
        foreach($plugins as $plugin){
            $services[]=$this->get($plugin);
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:playlistTemplate.html.twig',array('plugins'=>$services));
    }
    
    public function methodAction($serviceSufix, $method){
        if (!$plugin = $this->getPluginByServiceSufix($serviceSufix)){
            throw new Exception("Plugin not found");
        }
        
        if (!method_exists($plugin, $method)){
            throw new Exception("Method $method doesn't exist in ".  get_class($plugin));
        }
        
        
        return $plugin->$method($this->getRequest());
        
    }
}
