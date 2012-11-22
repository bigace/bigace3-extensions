<?php
/*
Plugin Name: YouTube Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:youtube
Description: A content parser plugin, which replaces [youtube]video-id[/youtube] tags in your page content with a YouTube Player.
Author: Kevin Papst
Version: 1.2
Author URI: http://www.kevinpapst.de/
$Id: youtube.php 22 2011-03-02 17:14:54Z kevin $
*/


class Plugin_YouTube implements Bigace_Plugin
{

    public function init()
    {
        Bigace_Hooks::add_filter('parse_content', array($this, 'parseContent'), 10, 2);
        Bigace_Hooks::add_action('youtube_plugin', array($this, 'getVersion'), 10, 2);
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
        $search = '/\[youtube\](.+)\[\/youtube\]/i';
        $find = preg_match($search, $content);
        if ($find !== false && $find > 0) {
            $title  = Bigace_Config::get('video', 'player.title', 'YouTube Video Player');
            $class  = Bigace_Config::get('video', 'player.class', 'youtube-player');
            $width  = Bigace_Config::get('video', 'player.width', '480');
            $height = Bigace_Config::get('video', 'player.height', '390');

            $code = '<iframe title="'.$title.'" class="'.$class.'" type="text/html" width="'.$width.
                    '" height="'.$height.'" src="http://www.youtube.com/embed/${1}" frameborder="0" allowFullScreen></iframe>';

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