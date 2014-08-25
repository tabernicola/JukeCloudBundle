<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

/**
 * Description of AbstractInfoPlugin
 *
 * @author Tabernicola
 */
abstract class AbstractInfoPlugin implements PluginInterface{
    
    abstract function getTemplateName();
    
    function getTemplateParameters(){
        return array();
    }
    
}
