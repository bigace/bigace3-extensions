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
 * @version    $Id: NewshomeController.php 193 2011-04-11 09:30:02Z kevin $
 */

/**
 * Controller to render the News Homepage.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_NewshomeController extends Bigace_Zend_Controller_Page_Action
{
    /**
     * Renders the News Homepage.
     * Sets all news and the news archives into the view.
     */
    public function indexAction()
    {
	    $to    = Bigace_Config::get("news", "homepage.amount", 10);
	    $lang  = Bigace_Config::get("news", "default.language");
	    //$lang  = $this->getMenu()->getLanguageID();
        $bns   = new Bigace_News_Service();
	    $all   = $bns->getLatest($to, $lang);
	    $items = array();

        foreach ($all as $news) {
            $image = null;

            if ($news->getImageID() !== null) {
                $image = Bigace_Item_Basic::get(_BIGACE_ITEM_IMAGE, $news->getImageID());
            }

		    $items[] = array(
		        'item'  => $news,
		        'image' => $image
		    );
        }

	    // whether or not we display the archives as listing entry
	    $this->view->showArchives = Bigace_Config::get("news", "homepage.archives");
        $this->view->NEWS         = $items;
        $this->view->ARCHIVES     = $bns->getArchives($lang);

	    $this->applyContent();
    }

}