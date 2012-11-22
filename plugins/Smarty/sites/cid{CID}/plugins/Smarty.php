<?php
/**
 * Bigace - a PHP and MySQL based Web CMS.
 *
 * LICENSE
 *
 * This source file is subject to the new GNU General Public License
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.bigace.de/license.html
 *
 * Bigace is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Smarty.php 190 2011-04-04 14:40:02Z kevin $
 */

/*
Plugin Name: Smarty Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:secure
Description: Activates the Smarty template engine for Bigace.
Author: Keleo
Version: 1.0
Author URI: http://www.keleo.de/
$Id: Smarty.php 190 2011-04-04 14:40:02Z kevin $
*/
class Plugin_Smarty implements Bigace_Plugin
{
    /**
     * @var Bigace_View_Engine_Smarty
     */
    private $view = null;

    /**
     * Activates the Plugin.
     */
    public function init()
    {
        // for historical reasons only!
        define('_BIGACE_CMD_SMARTY', 'smarty');

        $this->view = new Bigace_View_Engine_Smarty();

        // register smarty view in service factory
        Bigace_Services::get()->setService(
            Bigace_Services::VIEW_ENGINE, $this->view
        );

        // register smarty admin stuff
        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'));

        Bigace_Hooks::add_action('flush_cache', array($this, 'flushAll'));
        Bigace_Hooks::add_action('expire_page_cache', array($this, 'flushAll'));
    }

    /**
     * @see Bigace_Plugin::getVersion()
     */
    public function getVersion()
    {
        return "1.0";
    }

    /**
     * Creates all required empty directories and checks for existence of Smarty class.
     * @see library/Bigace/Bigace_Plugin#activate()
     */
    public function activate()
    {
        $smartyDir = BIGACE_3RDPARTY . 'Smarty/';
        if (!file_exists($smartyDir. 'libs/Smarty.class.php')) {
           return false;
        }

        import('classes.util.IOHelper');

        $activate = true;
        $dirs = array('cache/', 'configs/', 'templates/', 'templates_c/');
        foreach ($dirs as $d) {
            if (!file_exists($smartyDir.$d)) {
                if (!IOHelper::createDirectory($smartyDir.$d)) {
                    $activate = false;
                }
            }
        }

        return $activate;
    }

    /**
     * Used as callback for the 'admin_menu' hook.
     *
     * @return array
     */
    public function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('smarty');
        $menu['layout']['childs']['design'] = array('frights' => 'layout');
        $menu['layout']['childs']['templates'] = array('frights' => 'layout');
        $menu['layout']['childs']['stylesheets'] = array('frights' => 'layout');
        return $menu;
    }

    /**
     * Returns some credits for the Admin-About screen.
     *
     * @return array
     */
    public function getAdminCredits(array $credits)
    {
        $credits['Smarty'] = array(
            'Smarty' => array(
                'title'       => 'Smarty',
                'weblink'     => "http://www.smarty.net/",
                'copyright'   => "New Digital Group, Inc.",
                'description' => "Smarty is a template engine for PHP, facilitating a manageable
                                  way to separate logic and content from its presentation."
            ),
            'Smarty for Bigace' => array(
                'title'       => 'Smarty for Bigace',
                'weblink'     => "http://www.keleo.de/",
                'copyright'   => "Kevin Papst - Keleo",
                'description' => "An extension to enable Smarty templates for page rendering."
            ),
        );
        return $credits;
    }

    /**
     * Does nothing.
     */
    public function deactivate()
    {
    }

    /**
     * Flushes the complete smarty cache.
     */
    public function flushAll($which = null)
    {
        $this->view->getSmarty()->clearAllCache();
    }

    /**
     * Removes filter and actions from Bigace_Hooks.
     */
    public function __destruct()
    {
        Bigace_Hooks::remove_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::remove_filter('credits', array($this, 'getAdminCredits'));

        Bigace_Hooks::remove_action('flush_cache', array($this, 'flushAll'));
        Bigace_Hooks::remove_action('expire_page_cache', array($this, 'flushAll'));
    }
}