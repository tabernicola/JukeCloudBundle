<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractPlaylistPlugin;
use Tabernicola\JukeCloudBundle\Entity\Song;
use Symfony\Component\HttpFoundation\Response;

class YoutubePlugin extends AbstractPlaylistPlugin {
    
    public function getCssFiles() {
        return array();
    }

    public function getJavascriptFiles() {
        return array(
            'https://www.youtube.com/iframe_api',
            "bundles/tabernicolajukecloud/js/player/jc.youtube.js"
        );
    }

    public function getPluginTitle() {
        return "Youtube";
    }

    public function getPluginId() {
        return "youtube";
    }

    function getTemplateName(){
        return "TabernicolaJukeCloudBundle:Plugin/Templates:youtube.html.twig";
    }
    
    function getContent(Song $song) {
        return  new Response($song->getPath());
    }
    
}
