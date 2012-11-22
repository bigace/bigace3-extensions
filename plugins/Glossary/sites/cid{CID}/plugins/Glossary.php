<?php
/*
Plugin Name: Glossary Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:glossary
Description: This plugins brings an administration page and a modul.
Author: Keleo
Version: 0.1
Author URI: http://www.keleo.de/
$Id: Glossary.php 176 2011-03-25 14:56:37Z kevin $
*/

class Plugin_Glossary implements Bigace_Plugin
{
    public function init()
    {
        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'), 10, 1);
        Bigace_Hooks::add_filter('get_pagetypes', array($this, 'getPagetype'), 10, 1);
    }

    public function getVersion()
    {
        return "0.1";
    }

    // activates the glossary admin menu
    public function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('glossary');

        $menu['addon']['childs']['glossary'] = array(
                'permission' => 'glossary',
        );
        return $menu;
    }

    public function getAdminCredits(array $credits)
    {
        $credits['Glossary'] = array(
            'Glossary' => array(
                'title' => 'Glossary',
                'weblink' => "http://www.keleo.de/",
                'copyright' => "Keleo - Kevin Papst",
                'description' => "An extension to administrate and display glossary entries."
            )
        );
        return $credits;
    }

    public function getPagetype(array $pagetypes)
    {
        $pagetypes[] = 'glossary';
        return $pagetypes;
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }

}