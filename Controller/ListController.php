<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;
class ListController extends Controller
{
    public function artistsAction()
    {
        $request = $this->getRequest();
        $search=$request->query->get('term');
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Artist')
            ->search($search);
        return $this->elementsToResponse($songs);
    }
    
    public function disksAction()
    {
        $request = $this->getRequest();
        $search=$request->query->get('term');
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Disk')
            ->search($search);
        return $this->elementsToResponse($songs);
    }
    
    public function songsAction()
    {
        $request = $this->getRequest();
        $search=$request->query->get('term');
        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('TabernicolaJukeCloudBundle:Song')
            ->search($search);
        return $this->elementsToResponse($songs);
    }
    
    private function elementsToResponse($elements){
        $data=array();
        foreach ($elements as $element) {
            $node=new \stdClass();
            if(method_exists($element, 'getTitle')){
                $node->label=$element->getTitle();
            }
            else{
                $node->label=$element->getName();
            }
            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
}
