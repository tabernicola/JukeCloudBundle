<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Symfony\Component\DependencyInjection\Container;
use Tabernicola\JukeCloudBundle\Plugin\AbstractInfoPlugin;

class NewsPlugin extends AbstractInfoPlugin {
    
    private $container;
    
    function __construct(Container $container) {
        $this->container=$container;
    }
    
    public function getCssFiles() {
        return array();
    }

    public function getJavascriptFiles() {
        return array();
    }

    public function getPluginTitle() {
        return "Novedades";
    }

    public function getPluginId() {
        return "news";
    }

    function getTemplateName(){
        return "TabernicolaJukeCloudBundle:Plugin/Templates:news.html.twig";
    }
    
    function getTemplateParameters(){
        $em = $this->container->get('doctrine')->getManager();
        $repo=$em->getRepository('TabernicolaJukeCloudBundle:Song');
        $qb=$repo->createQueryBuilder('s');
        $qb ->groupBy('s.disk')
            ->orderBy('s.created','desc')
            ->setMaxResults( 5 );
        $result = $qb->getQuery()->getResult();
        return array('songs'=>$result);
    }
    
}
