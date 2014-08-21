<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tabernicola\JukeCloudBundle\Plugin\PluginInterface;

class PluginController extends Controller
{
    public function javascriptAction()
    {
        $plugins = $this->container->getParameter('tabernicola_juke_cloud.plugins');
        $files=array();
        foreach($plugins as $pluginsType){
            foreach($pluginsType as $plugin){
                /* var $service PluginInterface*/
                $service=$this->get($plugin);
                $files=array_merge($files,$service->getJavascriptFiles());
            }
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:js.html.twig',array('files'=>$files));
    }
    
    public function cssAction()
    {
        $plugins = $this->container->getParameter('tabernicola_juke_cloud.plugins');
        $files=array();
        foreach($plugins as $pluginsType){
            foreach($pluginsType as $plugin){
                /* var $service PluginInterface*/
                $service=$this->get($plugin);
                $files=array_merge($files,$service->getCssFiles());
            }
        }
        return $this->render('TabernicolaJukeCloudBundle:Plugin:js.html.twig',array('files'=>$files));
    }
    
    public function templateAction($type)
    {
        $plugins = $this->container->getParameter('tabernicola_juke_cloud.plugins');
        //if (isset($plugins[$type]))
        return $this->render('TabernicolaJukeCloudBundle:Plugin:'.$type.'Template.html.twig',array('templates'=>array()));
    }
}
