<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;
use Symfony\Component\Filesystem\Filesystem;
class InfoController extends Controller
{
    
    public function songAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $song = $em->getRepository('TabernicolaJukeCloudBundle:Song')->findOneById($id);
        $htmlResponse=new Response();
        if ($song){
            $disk=$song->getDisk();
            $artist=$song->getArtist();
            if (!$disk->getCover()){
                $srcPath=$this->getDiskCover($artist, $disk);
            }
            else{
                $srcPath=$disk->getCover();
            }
            
            // string to put directly in the "src" of the tag <img>
            $cacheManager = $this->container->get('liip_imagine.cache.manager');
            if ($srcPath){
                $srcPath = $cacheManager->getBrowserPath($srcPath, 'infocover');
            }
            else{
                $srcPath=$this->container->get('templating.helper.assets')
                    ->getUrl('bundles/tabernicolajukecloud/img/album_big.png');
            }
            
            $node=new \stdClass();
            $node->song = $song->getTitle();
            $node->disk = $disk->getTitle();
            $node->artist = $artist->getName();
            $node->cover = $srcPath;
            $response = new JsonResponse();
        }
        return $response->setData($node);
    }
    
    private function getDiskCover($artist, $disk){
        $discogs = $this->container->get('discogs');
        $response=$discogs->search(
            array('q'=>$artist->getName().' - '.$disk->getTitle())
        )->getPath('results');
        if (is_array($response) && count($response)){
            $root = $this->container->getParameter('tabernicola_juke_cloud.covers_dir');
            $em = $this->getDoctrine()->getManager();
            $fs = new Filesystem();
            $path='/'.date("Ym").'/'.$disk->getId().'.jpeg';
            
            $resul=$response[0];
            $thumb=str_replace("api.discogs.com","s.pixogs.com",$resul['thumb']);
            //$thumb=str_replace("R-90-","R-150-",$thumb);
            $fs->copy($thumb,$root.$path);
            //$genres=  array_merge($resul['style'], $resul['genre']);
            $disk->setCover($path);
            $em->persist($disk);
            $em->flush();
            return $path;
        }
        else{
            return false;
        }
    }
   
}
