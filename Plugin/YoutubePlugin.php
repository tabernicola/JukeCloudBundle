<?php

namespace Tabernicola\JukeCloudBundle\Plugin;

use Tabernicola\JukeCloudBundle\Plugin\AbstractPlaylistPlugin;

class InitInfoPlugin extends AbstractPlaylistPlugin {
    
    public function getCssFiles() {
        return array();
    }

    public function getJavascriptFiles() {
        return array(
            "'bundles/tabernicolajukecloud/js/jc.youtube.js'"
        );
    }

    public function getPluginTitle() {
        return "Youtube";
    }

    public function getPluginId() {
        return "youtube";
    }

    function getTemplateName(){
        return "TabernicolaJukeCloudBundle:Plugin/Templates:init-info.html.twig";
    }
    
}
