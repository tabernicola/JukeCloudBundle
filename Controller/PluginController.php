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
        return $this->render('TabernicolaJukeCloudBundle:Plugin:js.html.twig',array('files'=>$files));
    }
    
    public function renderInfoPluginAction()
    {
        $plugins = $this->getPlugins(array('info'));
        $services=array();
        foreach($plugins as $plugin){
            $services[]=$this->get($plugin);
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:infoTemplate.html.twig',array('plugins'=>$services));
    }
}
