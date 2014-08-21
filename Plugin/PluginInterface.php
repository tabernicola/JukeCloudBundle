<?php

namespace Tabernicola\JukeCloudBundle\Plugin;
/**
 *
 * @author Tabernicola
 */
interface PluginInterface {
    /**
     * Get an array with the css files to load
     * 
     * @return string[] array of strings
     */
    function getCssFiles();
    
    /**
     * Get an array with the js files to load
     * 
     * @return string[] array of strings
     */
    function getJavascriptFiles();
}
