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
 * @package    Bigace_Widget
 * @subpackage Impl
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: News.php 120 2011-03-15 15:10:20Z kevin $
 */

/**
 * As portlet for the BIGACE Web CMS, reading news from news extension.
 *
 * @todo       translate title and parameter
 * @category   Bigace
 * @package    Bigace_Widget
 * @subpackage Impl
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Widget_Impl_News extends Bigace_Widget_Abstract
{
	/**
	* Constructor
	*/
	public function __construct()
	{
        $this->setParameter(
            'amount', 3, Bigace_Widget::PARAM_INT, 'Amount of news (0=all)'
        );
        $this->setParameter(
            'link', true, Bigace_Widget::PARAM_BOOLEAN, 'Link to news [yes/no]'
        );
        $this->loadTranslation('news');
	}

    /**
     * The title of this portlet.
     * @return string
     */
    public function getTitle()
    {
        return $this->getTranslation('news_widget');
    }

    /**
     * The HTML, which is returned by this portlet.
     */
    public function getHtml()
    {
        $id   = Bigace_Config::get("news", "root.id");
        $lang = Bigace_Config::get("news", "default.language");
        $from = 0;
        $to = $this->getParameter('amount', 3);

        if ($id === null) {
            return '';
        }

        $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $id);
        $ir->setLanguageID($lang);
        $ir->setReturnType("Bigace_News_Item");
        $ir->setOrderBy("valid_from");
        $ir->setOrder('DESC');

        if ($to !== null && $to != 0) {
            $ir->setLimit($from, $to);
        }

        $allNews = new Bigace_Item_Walker($ir);

        if (count($allNews) > 0) {
	        $items = array();
	        $html = '<ul>';
	        $doLink = (bool)$this->getParameter('link');

	        foreach ($allNews as $temp) {
	            $html .= '<li>';
	            if ($doLink === true) {
					$html .= '<a href="'.LinkHelper::itemUrl($temp).'">'.
					         $temp->getName() . '</a><br/>'.
					         str_replace(chr(13), '<br/>', $temp->getTeaser());
				} else {
					$html .= '<b>'.$temp->getName() . '</b><br/>'.
					         str_replace(chr(13), '<br/>', $temp->getTeaser());
				}
                $html .= '</li>';
	        }
	        return $html . '</ul><br/>';
        }

        return '';
    }

}
