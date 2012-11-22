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
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Smarty.php 164 2011-03-25 09:39:42Z kevin $
 */

/**
 * View helper to load and execute BIGACE Smarty Tags.
 * Does not support the full smarty API but only the smallest thinkable
 * subset!
 *
 * This helper is meant to use Smarty TAGs (which are not yet migrated
 * to a View_Helper) in a Zend_Layout.
 *
 * Use it like this:
 * $topLevel = $this->smarty('load_item', array('id' => '-1', 'itemtype' => _BIGACE_ITEM_MENU));
 *
 * @category   Bigace
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Zend_View_Helper_Smarty extends Zend_View_Helper_Abstract
{
    public function smarty($function, array $params = array())
    {
        $func = 'smarty_function_'.$function;
        if (!function_exists($func)) {
            $ft = Bigace_Smarty_Core::getPluginsDirectory() . 'function.'.$function.'.php';
            if (!file_exists($ft)) {
                throw new Exception('Smarty TAG '.$function.' not existing. File is missing: ' . $ft);
            }

            require_once($ft);
        }

        $smarty = new Smarty_Helper_Assign();

        $result = call_user_func($func, $params, &$smarty);

        $pp = $smarty->getParams();
        if (count($pp) > 0) {
            // ??? FIXME what to do with these ???
        }

        return $result;
    }
}

/**
 * Class that fakes a Smarty object, as used in self developed smarty tags.
 * @access private
 */
class Smarty_Helper_Assign
{

    private $vars = array();

    public function assign($name, $value) {
        $this->vars[$name] = $value;
    }

    public function assign_by_ref($name, $value) {
        $this->vars[$name] = $value;
    }

    public function getParams() {
        return $this->vars;
    }

    public function trigger_error($string) {
        throw new Exception('Problem with Smarty TAG: ' . $string);
    }

}
