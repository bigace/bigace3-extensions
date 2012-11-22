<?php
/*
Plugin Name: Vimeo Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:youtube
Description: A content parser plugin, which replaces [vimeo]video-id[/vimeo] tags in your page content with a Vimeo Player.
Author: Kevin Papst
Version: 1.1
Author URI: http://www.kevinpapst.de/
$Id: $
*/

class Plugin_Vimeo implements Bigace_Plugin
{

    public function init()
    {
        Bigace_Hooks::add_filter('parse_content', array($this, 'parseContent'), 10, 2);
        Bigace_Hooks::add_action('vimeo_plugin', array($this, 'getVersion'), 10, 2);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return "1.1";
    }

    /**
     * @return string
     */
    public function parseContent($content, $menu)
    {
        $search = '/\[vimeo\](.+)\[\/vimeo\]/i';
        $find = preg_match($search, $content);
        if ($find !== false && $find > 0) {
            $title  = Bigace_Config::get('video', 'player.title', 'Vimeo Video Player');
            $class  = Bigace_Config::get('video', 'player.class', 'vimeo-player');
            $width  = Bigace_Config::get('video', 'player.width', '400');
            $height = Bigace_Config::get('video', 'player.height', '225');

            $code = '<iframe title="'.$title.'" class="'.$class.'" type="text/html" width="'.$width.
                    '" height="'.$height.'" src="http://player.vimeo.com/video/${1}" frameborder="0"></iframe>';

            $content = preg_replace($search, $code, $content);
        }
        return $content;
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }

}