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
 * @version    $Id: NewsController.php 130 2011-03-16 17:57:44Z kevin $
 */

/**
 * Controller to render a single News page.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_NewsController extends Bigace_Zend_Controller_Page_Action
{
    /**
     * Overwritten to allow a configurable default layout for a News item.
     *
     */
    public function getLayoutName()
    {
        $layout = Bigace_Config::get('news', 'layout', null);

        // if no special news template is configured, use default setting
        if ($layout === null || strlen(trim($layout)) == 0) {
            return parent::getLayoutName();
        }

        return $layout;
    }

    public function indexAction()
    {
        $menu = $this->getMenu();
        $id   = Bigace_Config::get("news", "root.id");
        $lang = Bigace_Config::get("news", "default.language");
        //$lang = $menu->getLanguageID();

        if ($id === null) {
            throw new Exception('The News system is not configured. Please set configuration key: news/root.id');
            return;
        }

        $news  = new Bigace_News_Item($menu->getID(), $menu->getLanguageID());
        $image = null;
        $type  = Bigace_Item_Type_Registry::get(_BIGACE_ITEM_MENU);
        $cs    = $type->getContentService();
        $cnt   = $cs->get($news, $cs->query());

        if ($news->getImageID() !== null) {
            $image = Bigace_Item_Basic::get(_BIGACE_ITEM_IMAGE, $news->getImageID());
        }

        $this->view->NEWS_CONTENT = $cnt->getContent();
        $this->view->NEWS         = $news;
        $this->view->IMAGE        = $image;

        $bns = new Bigace_News_Service();
        $this->view->ARCHIVES = $bns->getArchives($lang);

        $this->applyContent();
    }

}