<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractInfoPlugin;

class InitInfoPlugin extends AbstractInfoPlugin {
    
    public function getCssFiles() {
        return array();
    }

    public function getJavascriptFiles() {
        return array();
    }

    public function getPluginTitle() {
        return "JukeCloud Info";
    }

    public function getPluginId() {
        return "init-info";
    }

    function getTemplateName(){
        return "TabernicolaJukeCloudBundle:Plugin/Templates:init-info.html.twig";
    }
    
}
