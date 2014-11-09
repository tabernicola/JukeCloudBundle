<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $song->setPlayTimes($song->getPlayTimes()+1);
        $em->persist($song);
        $em->flush();

        $plugin = $this->container->get('tabernicola_juke_cloud.plugins.'.$song->getType());
        return $plugin->getContent($song);
        
    }
    
}
