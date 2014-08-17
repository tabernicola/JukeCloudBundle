<?php

namespace Tabernicola\JukeCloudBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tabernicola\JukeCloudBundle\Entity\Artist;
use Tabernicola\JukeCloudBundle\Entity\Disk;
use Tabernicola\JukeCloudBundle\Entity\Song;
use Gedmo\Sluggable\Util as Sluggable;

class LoadArtistData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var getID3
     */
    private $id3Reader;
    private $artistRepo;
    private $diskRepo;
    private $songRepo;
    
    public function __construct() {
        $this->id3Reader=new \getID3;
    }
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager, $root=null, $possibleArtistName=null, $possibleDiskName=null)
    {
        if (!$root){
            $root = $this->container->getParameter('tabernicola_juke_cloud.datadir');
            $this->artistRepo = $manager->getRepository('TabernicolaJukeCloudBundle:Artist');
            $this->diskRepo = $manager->getRepository('TabernicolaJukeCloudBundle:Disk');
            $this->songRepo = $manager->getRepository('TabernicolaJukeCloudBundle:Song');
            try{
                $manager->getConnection()->commit();
            } catch (Exception $e) {
                $manager->getConnection()->rollback();
            }
        }
        
        if (file_exists($root) && is_dir($root)){
            $dh = opendir( $root );
            if( false === $dh ) {
                return false;
            }
            while( $file = readdir( $dh )) {
                if( "." == $file || ".." == $file ){
                    continue;
                }
                if (is_dir($root.$file)){
                    if (!$possibleArtistName){
                        $possibleArtistName=$file;
                        $this->load($manager,$root.$file.'/',$possibleArtistName,$possibleDiskName);
                        $possibleArtistName='';
                    }
                    elseif (!$possibleDiskName){
                        $possibleDiskName=$file;
                        $this->load($manager,$root.$file.'/',$possibleArtistName,$possibleDiskName);
                        $possibleDiskName='';
                    }
                    
                }
                elseif(is_file($root.$file)){
                    $info=$this->id3Reader->analyze($root.$file);
                    if (!isset($info['error']) && isset($info['tags'])){
                        if(isset($info['tags']['id3v2'])){
                            $this->saveInfo($manager,$root,$file,$info['tags']['id3v2'],$possibleArtistName,$possibleDiskName);
                        }
                        elseif(isset($info['tags']['id3v1'])){
                            $info['tags']['id3v1']['track_number']=$info['tags']['id3v1']['track'];
                            $this->saveInfo($manager,$root,$file,$info['tags']['id3v1'],$possibleArtistName,$possibleDiskName);
                        }
                        elseif(isset($info['tags']['vorbiscomment'])){
                            $info['tags']['vorbiscomment']['track_number']=$info['tags']['vorbiscomment']['tracknumber'];
                            $this->saveInfo($manager,$root,$file,$info['tags']['vorbiscomment'],$possibleArtistName,$possibleDiskName);
                        }
                        elseif(isset($info['tags']['quicktime'])){
                            $this->saveInfo($manager,$root,$file,$info['tags']['quicktime'],$possibleArtistName,$possibleDiskName);
                        }
                        elseif(isset($info['tags']['asf'])){
                            //NOT YET SUPPORTED
                        }
                        else{    
                            echo "\n$root$file not suported";
                        }
                    }
                }
            }
            closedir( $dh );
            return true;
        }
    }
    
    private function saveInfo(ObjectManager $manager,$path,$file,$info,$possibleArtistName,$possibleDiskName){
        if ( !isset($info['track_number'][0]) ){
            list($val)=explode('.',$file,2);
            $val=preg_replace('/[a-zA-Z\s]/','',$val);
            $info['track_number'][0] = $val;
        }
        if (!isset($info['album'][0]) ){
            echo "\nCan't save $path/$file, no disk info";
            return;
        } 
        
        if (!isset($info['title'][0]) ){
            list($info['title'][0])=explode('.',$file);
        } 
        
        if (!isset($info['artist'][0])){
            echo "\nCan't save $path/$file, no artist info";
            return;
        } 
    
        $root="$path$file";
        if (file_exists($root) && !is_dir($root)){
            $manager->getConnection()->beginTransaction(); // suspend auto-commit
            $slug = strtolower(Sluggable\Urlizer::urlize($info['artist'][0], '-'));
            $artist=$this->artistRepo->findOneBySlug($slug);
            if (!$artist){
                $artist=new Artist();
                $artist->setName($info['artist'][0]);
                $manager->persist($artist);
            }
            
            $slug = strtolower(Sluggable\Urlizer::urlize($info['album'][0], '-'));
            $disk=$this->diskRepo->findOneBy(array('slug'=>$slug,'artist'=>$artist));
            if (!$disk){
                $disk=new Disk();
                $disk->setTitle($info['album'][0]);
                $disk->setArtist($artist);
                $manager->persist($disk);
            }
            
            $elem=new Song();
            $elem->setTitle($info['title'][0]);
            $elem->setNumber(intval($info['track_number'][0]));
            $elem->setArtist($artist);
            $elem->setDisk($disk);
            $elem->setPath($root);
            $disk->addSong($elem);
            $manager->persist($elem);
            echo  ".";
            $manager->flush();
            try{
                $manager->getConnection()->commit();
            } catch (Exception $e) {
                $manager->getConnection()->rollback();
            }
        }
    }
}
