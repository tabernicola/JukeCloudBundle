<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;
class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('TabernicolaJukeCloudBundle::index.html.twig',array());
    }
    
    public function typesAction()
    {
        $node=new \stdClass();
        $node->id= 'artists';
        $node->text= 'All the music';
        $node->type='root';
        $node->children = true;
        $obj=new \stdClass();
        $obj->opened=true;
        $node->state = $obj;
        $response = new JsonResponse();
        return $response->setData(array($node));

    }
    
    public function artistsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $artists = $em->getRepository('TabernicolaJukeCloudBundle:Artist')
                        ->findBy(array(), array('name' => 'ASC'));
        $i=0;
        $obj=new \stdClass();
        $obj->opened=false;
        foreach ($artists as $artist) {
            $i++;
            $node=new \stdClass();
            $node->id= 'artist-'.$artist->getId();
            $node->text= $artist->getName();
            $node->type='artist';
            $node->children = true;
            $node->state = $obj;

            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
    
    public function artistDisksAction($id)
    {
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $em = $this->getDoctrine()->getManager();

        $artist = $em->getRepository('TabernicolaJukeCloudBundle:Artist')->findOneById($id);
        $disks = $em->getRepository('TabernicolaJukeCloudBundle:Disk')
                        ->findBy(array('artist'=>$artist), array('title' => 'ASC'));
        $data=array();
        foreach ($disks as $disk) {
            $node=new \stdClass();
            $node->id= 'disk-'.$disk->getId();
            $node->text= $disk->getTitle();
            $node->type='disk';
            $node->children = true;
            if ($disk->getCover()){
                // string to put directly in the "src" of the tag <img>
                $srcPath = $cacheManager->getBrowserPath($disk->getCover(), 'navcover');
                $node->icon=$srcPath;
            }

            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
    
    public function diskSongsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $disk = $em->getRepository('TabernicolaJukeCloudBundle:Disk')->findOneById($id);
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')
                        ->findBy(array('disk'=>$disk), array('title' => 'ASC'));
        $data=array();
        foreach ($songs as $song) {
            $node=new \stdClass();
            $node->id= 'song-'.$song->getId();
            $node->text= $song->getTitle();
            $node->type='song';
            $node->children = false;

            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
    
    public function filterAction($search=null)
    {
        if (!$search){
            return $this->typesAction();
        }
        
        $em = $this->getDoctrine()->getManager();

        //states
        $openState=new \stdClass();
        $closeState=new \stdClass();
        $closeState->opened=false;        
        $openState->opened=true;
        
        //root node
        $root=new \stdClass();
        $root->id= 'artists';
        $root->text= 'All the music';
        $root->type='root';
        $childrens=array();
        
        //artists
        $artists = $em->getRepository('TabernicolaJukeCloudBundle:Artist')->search($search);
        foreach ($artists as $artist) {
            $childrens[$artist->getId()]=$artist->toObject();
            $childrens[$artist->getId()]->state=$closeState;
        }
        
        //disks
        $disks = $em->getRepository('TabernicolaJukeCloudBundle:Disk')->search($search);
        foreach ($disks as $disk){
            $artist=$disk->getArtist();
            if (!isset($childrens[$artist->getId()])){
                $childrens[$artist->getId()]=$artist->toObject();
                $childrens[$artist->getId()]->state=$openState;
            }
            $childrens[$artist->getId()]->children[$disk->getId()]=$disk->toObject();
            $childrens[$artist->getId()]->children[$disk->getId()]->state=$closeState;
        }
        
        //songs
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')->search($search);
        foreach ($songs as $song) {
            $artist=$song->getArtist();
            $disk=$song->getDisk();
            if (!isset($childrens[$artist->getId()])){
                $childrens[$artist->getId()]=$artist->toObject();
                $childrens[$artist->getId()]->state=$openState;
            }
            if (!isset($childrens[$artist->getId()]->children[$disk->getId()])){
                $childrens[$artist->getId()]->children[$disk->getId()]=$disk->toObject();
                $childrens[$artist->getId()]->children[$disk->getId()]->state=$openState;
            }
            $childrens[$artist->getId()]->children[$disk->getId()]->children[]=$song->toObject();
        }
        
        //convert hash array to index array
        $childrens=  array_values($childrens);
        $childrensCount=  count($childrens);
        for ($i= 0; $i < $childrensCount; $i++) {
            if (count($childrens[$i]->children)){
                $childrens[$i]->children=  array_values($childrens[$i]->children);
                $achildrensCount=  count($childrens[$i]->children);
                for ($j= 0; $j < $achildrensCount; $j++) {
                    if (count($childrens[$i]->children[$j]->children)){
                        $childrens[$i]->children[$j]->children=  array_values($childrens[$i]->children[$j]->children);
                    }
                }
            }
        }
        $root->children=$childrens;

        $response = new JsonResponse();
        return $response->setData(array($root));
    }
}
