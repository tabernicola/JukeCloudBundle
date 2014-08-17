<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;

class ContentController extends Controller
{
    public function songAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $song = $em->getRepository('TabernicolaJukeCloudBundle:Song')->findOneById($id);
        if (!$song) {
            throw $this->createNotFoundException('No song with id '.$id);
        }   

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
