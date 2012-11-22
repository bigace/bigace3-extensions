<?php
/*
Plugin Name: News Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:news
Description: This plugins brings a News administration page, RSS feed and several templates and includes for easy blog-like news integration for your website. Please read the <a href="http://wiki.bigace.de/bigace:extensions:addon:news" target="_blank">News Documentation</a> on how to install and configure this Plugin properly.
Author: Keleo
Version: 2.1
Author URI: http://www.keleo.de/
$Id: News.php 193 2011-04-11 09:30:02Z kevin $
*/

class Plugin_News implements Bigace_Plugin
{

    private $rootID = null;

    public function init()
    {
        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('get_pagetypes', array($this, 'getPagetype'), 10, 1);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'), 10, 1);
        Bigace_Hooks::add_action('news_plugin', array($this, 'getVersion'), 10, 1);
        Bigace_Hooks::add_action('init_view', array($this, 'initView'), 10, 1);

        $this->rootID = Bigace_Config::get("news", "root.id");
    }

    public function getPagetype(array $pagetypes)
    {
        $pagetypes[] = array('newshome', 'News-Homepage');
        $pagetypes[] = 'news';
        return $pagetypes;
    }

    public function getVersion()
    {
        return "2.1";
    }

    /**
     * Returns an array with information about the News extension.
     *
     * @return array
     */
    public function getAdminCredits(array $credits)
    {
        $credits['News'] = array(
            'News'            => array(
                'title'       => 'News',
                'weblink'     => "http://wiki.bigace.de/bigace:extensions:addon:news",
                'copyright'   => "Keleo - Kevin Papst",
                'description' => 'The News System was designed and implemented by Kevin Papst for
                              <a href="http://www.keleo.de">Keleo</a>. Please leave your feedback at
                              <a href="http://www.bigace.de/forum/">http://www.bigace.de/forum/</a>.'
            )
        );
        return $credits;
    }

    /**
     * Activates the news admin menu.
     *
     * @param array $menu
     * @param Bigace_Zend_Controller_Admin_Action $controller
     * @return array
     */
    public function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('news');

        $menu['menu']['childs']['news'] = array(
            'permission' => 'news'
        );
        return $menu;
    }

    /**
     * Adds news RSS links to the View header.
     *
     * @param Zend_View $view
     */
    public function initView(Zend_View $view)
    {
        $showFeed = Bigace_Config::get('news', 'feeds.enable', true);
        if ($showFeed === false) {
            return;
        }

        $feedUrl = Bigace_Config::get('news', 'feeds.custom.url', '');

        // a basic url needs more than 6 character, but if the user enters at least a
        // protocol, he gets what he wants ;)
        if (strlen(trim($feedUrl)) > 6) {
            $view->headLink(
                array(
                    'rel'   => 'alternate',
                    'href'  => $feedUrl,
                    'title' => 'News',
                    'type'  => 'application/rss+xml'
                )
            );

            return;
        }

        $view->headLink(
            array(
                'rel'   => 'alternate',
                'href'  => LinkHelper::url("news/feed/index/type/rss"),
                'title' => 'News (RSS 2.0)',
                'type'  => 'application/rss+xml'
            )
        );

        $view->headLink(
            array(
                'rel'   => 'alternate',
                'href'  => LinkHelper::url("news/feed/index/"),
                'title' => 'News (Atom)',
                'type'  => 'application/atom+xml'
            )
        );
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }

}
