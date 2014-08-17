<?php

namespace Tabernicola\JukeCloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tabernicola\JukeCloudBundle\Entity\Artist,
    Tabernicola\JukeCloudBundle\Entity\Disk,
    Tabernicola\JukeCloudBundle\Entity\Song;
use Tabernicola\JukeCloudBundle\Form\SongType;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends Controller
{
    public function indexAction(Request $request)
    {
        $uploadDir = $this->container->getParameter('tabernicola_juke_cloud.datadir');
        if ($request->getMethod() == 'POST') {
            $song=new Song();
            $obj=new \stdClass();
            $em = $this->getDoctrine()->getManager();

            $form = $this->createForm(new SongType, $song, array('em'=>$em));
            $form->handleRequest($request);
            $validator = $this->container->get('validator');
            $errorList = $validator->validate($song);
            if (!count($errorList)) {
                try{
                    $uploadDir.=$song->getArtist()->getName();
                    $uploadDir.='/'.$song->getDisk()->getTitle();
                    $song->upload($uploadDir);
                    $em = $this->getDoctrine()->getManager();
                    if (!$song->getDisk()->getArtist()){
                        $song->getDisk()->setArtist($song->getArtist());
                    }
                    $em->persist($song);
                    $em->flush();
                    $obj->url='song-'.$song->getId();
                    $obj->name=$song->getTitle();
                    $msg="Subida Realizada";
                    $class="alert alert-success";
                    /*{"files":[{"url":"http://jquery-file-upload.appspot.com/blank.gif",
                    *          "thumbnailUrl":"http://lh3.ggpht.com/C0FQdCPedquAF1S2o_hCf9WlMxEeTMs80",
                    *          "name":"blank.gif",
                    *          "type":"image/gif",
                    *          "size":43,
                    *          "deleteUrl":"http://jquery-file-upload.appspot.com/AMIBXU/blank.gif?delete=true",
                    *          "deleteType":"DELETE"}]}*/
                }
                catch(\Exception $e){
                  $obj->error=true;
                  $class="alert alert-danger";
                  $msg=$e->getMessage();
                }
            }
            else{
                $msg="";
                foreach ($errorList as $err) {
                    $msg.= $err->getMessage() . "\n";
                }
                $obj->error=true;
                $class="alert alert-danger";
                $msg=$msg;
            }
        }
        else{
            return new Response();
        }
        $id="rmLink-".rand(1, 10000);
        $closeBtn='<div><a id="'.$id.'" href="javascript:removeParentTr(\''.$id.'\')"><i class="right glyphicon glyphicon-remove"></i></a></div>';
        $link='';
        if (isset($obj->url)){
            $link='<div><a href="javascript:thePlaylist.addElement(\''.$obj->url.'\', $(thePlaylist.selector))">Añadir a la lista actual</a></div>';
        }
        $obj->response='<td colspan=9><div class="'.$class.' large100">'.$closeBtn.'<div>'.$msg.'</div>'.$link.'</div></td>';
        
        $data=new \stdClass();
        $data->files=array();
        $data->files[]=$obj;
        $response = new JsonResponse();
        return $response->setData($data);
    }
    
    public function artistsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $artists = $em->getRepository('TabernicolaJukeCloudBundle:Artist')
                        ->findBy(array(), array('name' => 'ASC'));
        foreach ($artists as $artist) {
            $node=new \stdClass();
            $node->id= 'artist-'.$artist->getId();
            $node->text= $artist->getName();
            $node->type='artist';
            $node->children = true;

            $data[]=$node;
        }
        $response = new JsonResponse();
        return $response->setData($data);
    }
    
    

}
