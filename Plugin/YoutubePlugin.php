<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractPlaylistPlugin;
use Tabernicola\JukeCloudBundle\Entity\Song;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Madcoda\Youtube;
use Tabernicola\JukeCloudBundle\Form\YoutubeSongType;
use Symfony\Component\DependencyInjection\Container;

class YoutubePlugin extends AbstractPlaylistPlugin {
    private $apiKey;
    private $container;
    
    function __construct(Container $container, $apiKey) {
        $this->container=$container;
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
        return "TabernicolaJukeCloudBundle:Plugin/Templates/youtube:youtube.html.twig";
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
            $em = $this->container->get('doctrine')->getManager();
            $repo=$em->getRepository('TabernicolaJukeCloudBundle:Song');
            if($song=$repo->findOneBy(array('path'=>$videoId,'type'=>$this->getPluginId()))){
                $html=$this->container->get('templating')->render(
                    "TabernicolaJukeCloudBundle:Plugin/Templates/youtube:error.video-exist.html.twig",
                    array('id'=>$videoId,'song'=>$song)
                );
                return new JsonResponse(array('error'=>$html));
            }
            // Return a std PHP object 
            $video = $youtube->getVideoInfo($videoId);
            return new JsonResponse($video);
            
        } catch (\Exception $e) {
            return new JsonResponse(array('error'=>$e->getMessage()));
        }
    }
    
    function add(Request $request){
        if ($request->getMethod() == 'POST') {
            $song=new Song();
            $obj=new \stdClass();
            $em = $this->container->get('doctrine')->getManager();
            $form = $this->container->get('form.factory')->create(new YoutubeSongType, $song, array('em'=>$em));
            $form->handleRequest($request);
            $validator = $this->container->get('validator');
            $errorList = $validator->validate($song);
            if (!count($errorList)) {
                $path=$request->request->get('ytId');
                $song->setPath($path);
                $song->setType($this->getPluginId());
                if (!$song->getDisk()->getArtist()){
                    $song->getDisk()->setArtist($song->getArtist());
                }
                $em->persist($song);
                $em->flush();
                $obj->url='song-'.$song->getId();
                $obj->name=$song->getTitle();
                $html=$this->container->get('templating')->render(
                    "TabernicolaJukeCloudBundle:Plugin/Templates/youtube:succes.html.twig",
                    array('song'=>$song)
                );
                return new JsonResponse(array('msg'=>$html));
            } else{
                $msg="";
                foreach ($errorList as $err) {
                    $msg.= $err->getMessage() . "\n";
                }
                $html=$this->container->get('templating')->render(
                    "TabernicolaJukeCloudBundle:Plugin/Templates/youtube:error.html.twig",
                    array('msg'=>$msg)
                );
                return new JsonResponse(array('error'=>$html));
            }
        }
        else{
            return new JsonResponse();
        }
    }
}
