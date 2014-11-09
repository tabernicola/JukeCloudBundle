<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractPlaylistPlugin;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tabernicola\JukeCloudBundle\Entity\Song;

class LocalPlugin extends AbstractPlaylistPlugin {
    private $componentsFolder;
    
    public function __construct($componentsFolder) {
        $this->componentsFolder=$componentsFolder;
    }
    
    public function getCssFiles() {
        return array(
            "/blueimp-fileupload/css/jquery.fileupload.css",
            "/blueimp-fileupload/css/jquery.fileupload-ui.css"
        );
    }

    public function getJavascriptFiles() {
        return array(
            $this->componentsFolder . 'jPlayer/jquery.jplayer/jquery.jplayer.js',
            "bundles/tabernicolajukecloud/js/player/jc.jplayer.js"
        );
    }

    public function getPluginTitle() {
        return "Sube Canciones";
    }

    public function getPluginId() {
        return "local";
    }

    function getTemplateName(){
        return "TabernicolaJukeCloudBundle:Plugin/Templates:local.html.twig";
    }
    
    function getContent(Song $song) {
        $response=new BinaryFileResponse($song->getPath());
        $mime=mime_content_type($song->getPath());
        //if mimetype is data, change to audio
        if ($mime=='application/octet-stream'){
            $mime='audio/mpeg';
        }
        $response->headers->set('Content-Type', $mime);
        return $response;
    }
    
}
