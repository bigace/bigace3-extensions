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
 * @version    $Id: Highlighter.php 689 2011-03-18 10:41:03Z kevin $
 */

/**
 * View helper to enable and add a Highlighter to the template.
 *
 * Please note, that this ViewHelper relies on the headScript() and
 * headLink() ViewHelper. Your Layout needs to make use of them, if you want
 * to utilize this highlighter.
 *
 * Add any language to highlight by using <code>addBrush()</code>.
 *
 * Also note, that you have to call <code>activate()</code>
 * before the mentioned "head" ViewHelper are called!
 *
 * An example call could look like this:
 * <code>$this->highlighter()->addBrush('Php')->useTheme('Eclipse')->activate();</code>
 *
 * @category   Bigace
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Zend_View_Helper_Highlighter extends Zend_View_Helper_Abstract
{
	/**
     * @var string
     */
	private $brushes = array();
    /**
     * @var string
     */
	private $theme = 'Default';

	const THEME_DEFAULT    = 'Default';
	const THEME_DJANGO     = 'Django';
	const THEME_ECLIPSE    = 'Eclipse';
	const THEME_EMACS      = 'Emacs';
	const THEME_FADETOGREY = 'FadeToGrey';
	const THEME_MDULTRA    = 'MDUltra';
	const THEME_MIDNIGHT   = 'Midnight';
	const THEME_RDARK      = 'RDark';

	/**
     * @return Bigace_Zend_View_Helper_Highlighter
     */
	public function highlighter()
	{
		return $this;
	}

	/**
     * Adds an brush.
     *
     * @param string $brush
     * @return Bigace_Zend_View_Helper_Highlighter
     */
	public function addBrush($brush)
	{
		$this->brushes[] = $brush;
		return $this;
	}

	/**
     * Set an theme.
     *
     * @param string $theme
     * @return Bigace_Zend_View_Helper_Highlighter
     */
	public function setTheme($theme)
	{
		$this->theme = $theme;
		return $this;
	}

	/**
	 * Activates the Highligther for the current layout.
     */
	public function activate()
	{
		$folder = $this->view->directory('public') . 'highlighter/';
		if (count($this->brushes) == 0) {
			return;
		}

		$this->view->headScript()->appendFile($folder.'scripts/shCore.js');

		foreach ($this->brushes as $name) {
			$html = $this->view->headScript()->appendFile($folder.'scripts/shBrush'.$name.'.js');
		}

		$this->view->headLink()->appendStylesheet($folder.'styles/shCore'.$this->theme.'.css');
		$this->view->headScript()->appendScript('SyntaxHighlighter.all()');
	}

}