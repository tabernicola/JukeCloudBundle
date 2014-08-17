<?php

namespace Tabernicola\JukeCloudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disk
 */
class Disk
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $songs;

    /**
     * @var \Tabernicola\JukeCloudBundle\Entity\Artist
     */
    private $artist;

        /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $cover='';

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->songs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCover('');
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Disk
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Disk
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add songs
     *
     * @param \Tabernicola\JukeCloudBundle\Entity\Song $songs
     * @return Disk
     */
    public function addSong(\Tabernicola\JukeCloudBundle\Entity\Song $songs)
    {
        $this->songs[] = $songs;

        return $this;
    }

    /**
     * Remove songs
     *
     * @param \Tabernicola\JukeCloudBundle\Entity\Song $songs
     */
    public function removeSong(\Tabernicola\JukeCloudBundle\Entity\Song $songs)
    {
        $this->songs->removeElement($songs);
    }

    /**
     * Get songs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * Set artist
     *
     * @param \Tabernicola\JukeCloudBundle\Entity\Artist $artist
     * @return Disk
     */
    public function setArtist(\Tabernicola\JukeCloudBundle\Entity\Artist $artist = null)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return \Tabernicola\JukeCloudBundle\Entity\Artist 
     */
    public function getArtist()
    {
        return $this->artist;
    }
    
    public function toObject(){
        $node=new \stdClass();
        $node->id= 'disk-'.$this->getId();
        $node->text= $this->getTitle();
        $node->type='disk';
        $node->children = array();
        return $node;
    }
    
    public function __toString() {
        return "\nDisk: ".$this->getTitle();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Disk
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Disk
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Disk
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    

    /**
     * Set cover
     *
     * @param string $cover
     * @return Disk
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        return $this->cover;
    }
}
