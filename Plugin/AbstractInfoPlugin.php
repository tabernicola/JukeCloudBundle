<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

/**
 * Description of AbstractInfoPlugin
 *
 * @author Tabernicola
 */
abstract class AbstractInfoPlugin implements PluginInterface{
    function getTemplateParameters(){
        return array();
    }
}
