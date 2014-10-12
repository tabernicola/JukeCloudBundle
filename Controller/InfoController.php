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
    
    private function getDiskCover(Artist $artist, Disk $disk){
        $jcConfig = $this->container->getParameter('tabernicola_juke_cloud.config');
        if (!isset($jcConfig['lastfm_apikey']) ||  !$jcConfig['lastfm_apikey'] ||
            !isset($jcConfig['lastfm_secret']) || !$jcConfig['lastfm_secret']){
            return '';
        }
        $lastFmApiKey=$jcConfig['lastfm_apikey'];
        $lastFmSecret=$jcConfig['lastfm_secret'];
        $lastfm = new \Dandelionmood\LastFm\LastFm( $lastFmApiKey, $lastFmSecret );
        $response=$lastfm->album_search(array('album'=>$disk->getTitle()));
        $albums=$response->results->albummatches;
        $album=$image=null;
        if(is_object($albums)){
            $album=$albums->album;
        }
        
        if (is_array($album)){
            $album=$this->getBestMatchByArtistName($album, $artist->getName());
        }
        if ($album){
            $image=$this->getBestImage($album->image);
        }
        if ($image){
            $root = $this->container->getParameter('tabernicola_juke_cloud.covers_dir');
            $em = $this->getDoctrine()->getManager();
            $fs = new Filesystem();
            $path='/'.date("Ym").'/'.$disk->getId().'.jpeg';
            
            //$thumb=str_replace("R-90-","R-150-",$thumb);
            $fs->copy($image,$root.$path);
            //$genres=  array_merge($resul['style'], $resul['genre']);
            if (file_exists($root.$path) && filesize($root.$path)>1024){
                $disk->setCover($path);
                $em->persist($disk);
                $em->flush();
                return $path;
            }
        }
        return false;
    }
    
    private function getBestMatchByArtistName($albums, $artist){
        $artist=  strtolower($artist);
        $currentProximity=-1;
        $currentMatches=-1;
        $bestMatch=null;
        foreach ($albums as $album){
            
            $matches=similar_text(strtolower($album->artist), $artist, $proximity);
            if ($proximity>$currentProximity || ($proximity==$currentProximity && $matches==$currentMatches)){
                $currentProximity=$proximity;
                $currentMatches=$matches;
                $bestMatch=$album;
            }
            if ($proximity>95){
                break;
            }
        }
        return $bestMatch;
    }
    
    private function getBestImage($images){
        $currentSizePriority=0;
        $prioritys=array('large'=>2, 'extralarge'=>1);
        $bestImage=null;
        foreach ($images as $image){
            if (isset($prioritys[$image->size]) && $prioritys[$image->size]>$currentSizePriority){
                $bestImage=$image->{"#text"};
            }
        }
        return $bestImage;
    }
}
