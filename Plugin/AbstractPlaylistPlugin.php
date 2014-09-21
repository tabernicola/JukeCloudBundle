<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Entity\Song;

/**
 * Description of AbstractPlaylistPlugin
 *
 * @author Tabernicola
 */
abstract class AbstractPlaylistPlugin implements PluginInterface{
    function getTemplateParameters(){
        return array();
    }
    
    abstract function getContent(Song $song);
}
