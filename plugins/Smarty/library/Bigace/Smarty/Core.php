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
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Core.php 190 2011-04-04 14:40:02Z kevin $
 */

if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', BIGACE_3RDPARTY . 'Smarty/libs/');
    require_once(SMARTY_DIR . 'Smarty.class.php');
}

/**
 * Always use this class when using Smarty Templates.
 * Do not instantiate Smarty directly, this instance is fully configured to use
 * the current Community settings.
 *
 * It takes care about loadig the Smarty Engine and about setting the proper environment.
 *
 * @category   Bigace
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Smarty_Core extends Smarty
{
    /**
     * Contains the realpath where the layouts are stored.
     *
     * @var string
     */
    private $folder = null;

    public function __construct()
    {
        parent::__construct();

        $path = $this->getFolder();

        // also used in Bigace_Smarty_Template, change both!
        $this->template_dir = $path . 'views/smarty/';
        $this->compile_dir  = $path . 'cache/';
        $this->config_dir   = $path . 'config/';
        $this->cache_dir    = $path . 'cache/';

        $this->addPluginsDir(self::getPluginsDirectory());
        $this->addPluginsDir(SMARTY_DIR . 'plugins/');
    }

    /**
     * Returns the directory where Bigace specific Smarty Plugins are stored.
     *
     * @return string
     */
    public static function getPluginsDirectory()
    {
        return BIGACE_3RDPARTY . 'Bigace/Smarty/Plugins/';
    }

    /**
     * Returns the folder where layouts are stored.
     *
     * @return string
     */
    protected function getFolder()
    {
        if ($this->folder === null) {
            if (!Zend_Registry::isRegistered('BIGACE_COMMUNITY')) {
                throw new Bigace_View_Exception('Community was null, cannot detect smarty folder.');
            }
            $community    = Zend_Registry::get('BIGACE_COMMUNITY');
            $this->folder = $community->getPath();
        }
        return $this->folder;
    }

}