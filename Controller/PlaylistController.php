<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;
class PlaylistController extends Controller
{
    public function artistAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')
            ->findBy(array("artist"=>$id), array('disk' => 'DESC','id'=>'DESC'));
        return $this->songsToResponse($songs);
    }
    
    public function diskAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')
            ->findBy(array('disk'=>$id), array('id' => 'DESC'));
        return $this->songsToResponse($songs);
    }
    
    public function songAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')
            ->findById($id);
        return $this->songsToResponse($songs);
    }
    
    public function randomSongAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('TabernicolaJukeCloudBundle:Song');
        $max=$repo->getMaxId();
        $min=$repo->getMinId();
        $val=rand($min, $max);
        $song=$repo->findClosestById($val);
        return $this->songsToResponse(array($song));
    }
    
    private function songsToResponse($songs){
        $data=array();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        
        foreach ($songs as $song) {
            $node=new \stdClass();
            $node->id= 'song-'.$song->getId();
            $node->songTitle= $song->getTitle();
            $node->diskTitle= $song->getDisk()->getTitle();
            if ($song->getDisk()->getCover()){
                // string to put directly in the "src" of the tag <img>
                $srcPath = $cacheManager->getBrowserPath($song->getDisk()->getCover(), 'plcover');
                $node->icon=$srcPath;
            }
            else{
                $node->icon=$this->container->get('templating.helper.assets')
                            ->getUrl('bundles/tabernicolajukecloud/img/album.png');
            }
            $node->artistName= $song->getArtist()->getName();
            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
}
