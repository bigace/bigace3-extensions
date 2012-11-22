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
 * @version    $Id: Secure.php 176 2011-03-25 14:56:37Z kevin $
 */

/*
Plugin Name: Secure Download Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:secure
Description: This plugins brings an administration page and an entry for the credits section.
Author: Keleo
Version: 0.1
Author URI: http://www.keleo.de/
$Id: Secure.php 176 2011-03-25 14:56:37Z kevin $
*/
class Plugin_Secure implements Bigace_Plugin
{
    public function init()
    {
        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'), 10, 1);
    }

    public function getVersion()
    {
        return "0.1";
    }

    // activates the news admin menu
    function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('secure');

        $menu['addon']['childs']['secure'] = array(
                'permission' => 'securedownload',
        );
        return $menu;
    }

    function getAdminCredits(array $credits)
    {
        $credits['Secure Download'] = array(
            'Secure Download' => array(
                'title' => 'Secure Download',
                'weblink' => "http://www.keleo.de/",
                'copyright' => "Kevin Papst - Keleo",
                'description' => "An extension to secure file downloads with random and self-expirying links."
            )
        );
        return $credits;
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }
}