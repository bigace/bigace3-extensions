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
 * @version    $Id: ArchiveController.php 134 2011-03-17 11:42:59Z kevin $
 */

/**
 * Controller to show news archive listings.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class News_ArchiveController extends Bigace_Zend_Controller_Page_Action
{
    // make sure we use the news homepage as current menu
    protected function preInit()
    {
        import('classes.menu.MenuService');

        $service = new MenuService();
        $id      = Bigace_Config::get("news", "root.id");
        $lang    = Bigace_Config::get("news", "default.language");

        if ($id === null) {
            throw new Bigace_Exception(
                'The News system is not configured. Please set configuration key: news/root.id'
            );
            return;
        }

        // Load the requested Menu and set it as global variable
        $this->setMenu($service->getMenu($id, $lang));
    }

    public function indexAction()
    {
        $id   = Bigace_Config::get("news", "root.id");
        $lang = Bigace_Config::get("news", "default.language");

        if ($id === null) {
            throw new Bigace_Exception(
                'The News system is not configured. Please set configuration key: news/root.id'
            );
            return;
        }

        $temp = basename($this->getRequest()->getRequestUri());
        $pos  = strpos($temp, '-');

        if ($pos === false) {
            throw new Exception('Invalid news archive requested');
            return;
        }

        $year  = substr($temp, 0, $pos);
        $month = substr($temp, $pos+1);

        if (strcmp(intval($year), $year) !== 0) {
            throw new Exception('Invalid news year requested');
            return;
        }

        if (strcmp(intval($month), $month) !== 0 && strcmp('0'.intval($month), $month) !== 0 ) {
            throw new Exception('Invalid news month requested');
            return;
        }

        $req = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $id);
        $req->setLanguageID($lang)
            ->setReturnType('Bigace_News_Item')
            ->setOrderBy('valid_from')
            ->setOrder('DESC');

        // calculate the correcte archive duration
        $archiveTs = 0;
        $archiveTs = mktime(0, 0, 0, $month, 1, $year);
        $lastTS    = strtotime("+1 month", $archiveTs);

        $req->where('a.valid_from >= '.$archiveTs)
            ->where('a.valid_from <= '.$lastTS);

        $allNews = new Bigace_Item_Walker($req);
        $items   = array();

        foreach ($allNews as $news) {
            $image = null;

            if ($news->getImageID() !== null) {
                $image = Bigace_Item_Basic::get(_BIGACE_ITEM_IMAGE, $news->getImageID());
            }

            $items[] = array(
                'item'  => $news,
                'image' => $image
            );
        }

        $bns = new Bigace_News_Service();

        $this->view->ARCHIVES   = $bns->getArchives($lang);
        $this->view->NEWS       = $items;
        $this->view->YEAR       = $year;
        $this->view->MONTH      = $month;
        $this->view->ARCHIVE_TS = $archiveTs;

        $this->render('newshome/index', null, true);
    }

}