<?php
/*
Plugin Name: Jobs Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:jobs
Description: This plugins brings an administration page and a frontend controller AKA Pagetype.
Author: Keleo
Version: 1.0
Author URI: http://www.keleo.de/
$Id: Jobs.php 176 2011-03-25 14:56:37Z kevin $
*/

class Plugin_Jobs implements Bigace_Plugin
{
    public function init()
    {
        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'), 10, 1);
        Bigace_Hooks::add_filter('get_pagetypes', array($this, 'getPagetype'), 10, 1);
    }

    public function getVersion()
    {
        return "1.0";
    }

    // activates the jobs admin menu
    public function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('jobs');

        $menu['addon']['childs']['jobs'] = array(
                'permission' => 'jobs',
        );
        return $menu;
    }

    public function getAdminCredits(array $credits)
    {
        $credits['Jobs'] = array(
            'Jobs' => array(
                'title' => 'Jobs',
                'weblink' => "http://www.keleo.de/",
                'copyright' => "Keleo - Kevin Papst",
                'description' => "An extension to administrate and display Job vacancies."
            )
        );
        return $credits;
    }

    public function getPagetype(array $pagetypes)
    {
        $pagetypes[] = 'jobs';
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
