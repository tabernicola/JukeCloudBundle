<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractPlaylistPlugin;
use Tabernicola\JukeCloudBundle\Entity\Song;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Madcoda\Youtube;

class YoutubePlugin extends AbstractPlaylistPlugin {
    private $apiKey;
    
    function __construct($apiKey) {
        if (!$apiKey){
            throw new Exception("You must set tabernicola_juke_cloud.youtube_plugin.apikey configuration key if you want to use the youtube plugin ");
        }
        $this->apiKey=$apiKey;
    }
    
    public function getCssFiles() {
        return array(
            "bundles/tabernicolajukecloud/css/playlist/jc.youtube.css"
        );
    }

    public function getJavascriptFiles() {
        return array(
            'https://www.youtube.com/iframe_api',
            "bundles/tabernicolajukecloud/js/player/jc.youtube.js",
            "bundles/tabernicolajukecloud/js/song-types/jc.youtube.js"
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
    
    function info(Request $request){
        $url=$request->request->get('url');
        try{
            $youtube = new Youtube(array('key' => $this->apiKey));

            // Parse Youtube URL into videoId
            $videoId = $youtube->parseVIdFromURL($url);
            // Return a std PHP object 
            $video = $youtube->getVideoInfo($videoId);
            return new JsonResponse($video);
            
        } catch (\Exception $e) {
            return new JsonResponse(json_encode(array(
                'error'=>$e->getMessage() 
            )));
        }
        
        
    }
}
