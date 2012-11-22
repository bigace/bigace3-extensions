<?php
/*
Plugin Name: Social Bookmarks
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:socialbookmark
Description: This plugins displays a beautyful social bookmark menu.
Author: Kevin Papst
Version: 1.0
Author URI: http://www.kevinpapst.de/
$Id: social-bookmark-menu.php 4 2011-01-31 13:41:05Z kevin $
*/


class Plugin_SocialBookmarks implements Bigace_Plugin
{
    /**
     * @var array
     */
    private $config = null;

    public function init()
    {
        Bigace_Hooks::add_action('social_bookmark_plugin', array($this, 'getVersion'), 10, 1);
        Bigace_Hooks::add_filter('parse_content', array($this, 'parseContent'), 10, 2);
        Bigace_Hooks::add_action('init_view', array($this, 'initView'), 10, 1);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return "1.0";
    }

    /**
     * @return array
     */
    protected function getSiteTitles()
    {
        return array(
            'digg'          => 'Digg this!',
            'reddit'        => 'Share this on Reddit',
            'stumbleupon'   => 'Stumble upon something good? Share it on StumbleUpon',
            'delicious'     => 'Share this on delicious',
            'technorati'    => 'Share this on Technorati',
            'furl'          => 'Share this on Furl',
            'facebook'      => 'Share this on Facebook',
            'myspace'       => 'Post this to MySpace',
            'yahoo'         => 'Save this to Yahoo MyWeb',
            'script-style'  => 'Submit this to Script & Style',
            'blinklist'     => 'Share this on Blinklist',
            'mixx'          => 'Share this on Mixx',
            'designfloat'   => 'Submit this to DesignFloat',
        );
    }

    /**
     * Returns an array with all supported social bookmark pages.
     *
     * @return array
     */
    protected function getSites()
    {
        return array(
            'digg'          => 'http://digg.com/submit?phase=2&url=%s&title=%s',
            'reddit'        => 'http://reddit.com/submit?url=%s&title=%s',
            'stumbleupon'   => 'http://www.stumbleupon.com/submit?url=%s&title=%s',
            'delicious'     => 'http://del.icio.us/post?url=%s&title=%s',
            'technorati'    => 'http://technorati.com/faves?add=%s',
            'furl'          => 'http://www.furl.net/storeIt.jsp?u=%s&t=%s',
            'facebook'      => 'http://www.facebook.com/share.php?u=%s&amp;t=%s',
            'myspace'       => 'http://www.myspace.com/Modules/PostTo/Pages/?u=%s&amp;t=%s',
            'yahoo'         => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=%s&t=%s',
            'script-style'  => 'http://scriptandstyle.com/submit?url=%s&title=%s',
            'blinklist'     => 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&Url=%s&Title=%s',
            'mixx'          => 'http://www.mixx.com/submit?page_url=%s&amp;title=%s',
            'designfloat'   => 'http://www.designfloat.com/submit.php?url=%s&amp;title=%s',
        );
    }

    /**
     * Returns the global configuration of this plugin.
     *
     * @return array
     */
    protected function getConfiguration()
    {
        if ($this->config === null) {
            $temp  = Bigace_Config::preload('social-bookmarks');
            $sites = 'digg,reddit,stumbleupon,delicious,technorati,furl,facebook,' .
                     'myspace,yahoo,script-style,blinklist,mixx,designfloat';
            $temp  = Bigace_Config::get('social-bookmarks', 'sites', $sites);

            // if empty, all available services will be offered
            if (strlen(trim($temp)) == 0) {
                $temp = $sites;
            }

            $this->config = array(
                'css'   => Bigace_Config::get('social-bookmarks', 'internal.css', true),
                'sites' => $temp
            );
        }
        return $this->config;
    }

    /**
     * Adds news RSS links to the View header.
     *
     * @param Zend_View $view
     */
    public function initView(Zend_View $view)
    {
        $config = $this->getConfiguration();
        if ($config['css']) {
            $folder = $view->directory('public');
            $view->headLink()->appendStylesheet($folder . 'socialbookmarks/styles.css');
            $view->headStyle()->appendStyle("ul.socials a { color:#fff; }", array('conditional' => 'IE'));
            $view->headScript()->appendFile(
                $folder . 'socialbookmarks/IE7.js', 'text/javascript', array('conditional' => 'lt IE 7')
            );
        }
    }

    /**
     * Parses the page content to find and replace the [socialbookmarks] code.
     *
     * @param string $content
     * @param Bigace_Item $menu
     * @return string
     */
    public function parseContent($content, $menu)
    {
        $config = $this->getConfiguration();
        $url    = urlencode(LinkHelper::itemUrl($menu));
        $title  = $menu->getName();
        $sites  = $this->getSites();
        $titles = $this->getSiteTitles();

        // if the user wants to use the global configuration
        $search = '/\[socialbookmarks\/]/i';
        $find   = preg_match($search, $content);
        if ($find !== false && $find > 0) {
            $active  = explode(',', $config['sites']);
            if (count($active) == 0) {
                $active = array_keys($sites);
            }
            $html = $this->prepareSiteHtml($sites, $titles, $active, $url, $title);
            return preg_replace($search, $html, $content);
        }

        // if the user wants per-page configuration
        $search = '/\[socialbookmarks\](.+)\[\/socialbookmarks\]/i';
        $find   = preg_match($search, $content, $result);
        if ($find !== false && $find > 0) {
            $active  = explode(',', $result[1]);
            if (count($active) == 0) {
                $active = array_keys($sites);
            }
            $html = $this->prepareSiteHtml($sites, $titles, $active, $url, $title);
            return preg_replace($search, $html, $content);
        }

        // fallback if no social bookmark was requested
        return $content;
    }

    /**
     * @return string
     */
    protected function prepareSiteHtml($sites, $titles, $active, $url, $title)
    {
        $html = '<ul class="socials">';

        foreach ($active as $key) {
            $sburl = sprintf($sites[$key], $url, $title);
            $html .= '<li class="'.$key.'">' .
                     '<a target="_blank" rel="nofollow" href="'.$sburl.'" title="'.$titles[$key].'">' .
                     $titles[$key] . '</a></li>';
        }

        $html .= '</ul><div class="socialclear">&nbsp;</div>';
        return $html;
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }

}