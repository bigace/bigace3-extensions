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
 * @version    $Id: CategoryController.php 24 2010-12-14 10:57:28Z kevin $
 */

/**
 * Controller to show all news within a given category.
 *
 * FIXME does not work yet - no view existing
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class News_CategoryController extends Bigace_Zend_Controller_Page_Action
{
    // make sure we use the news homepage as current menu
    protected function preInit()
    {
        import('classes.category.CategoryService');
        import('classes.menu.MenuService');
        $mService = new MenuService();
	    $id = Bigace_Config::get("news", "root.id");
	    $lang = Bigace_Config::get("news", "default.language");

	    if ($id === null) {
	        throw new Exception('The News system is not configured. Please set configuration key: news/root.id');
		    return;
	    }

        // Load the requested Menu and set it as global variable
        $this->setMenu($mService->getMenu($id, $lang));
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $temp = basename($request->getRequestUri());
        $pos = strpos($temp, '-');

        if ($pos === false) {
            throw new Bigace_Exception("Invalid News category given");
        }

        $cs = new CategoryService();
        $category = null;

        $uid = substr($temp, 0, $pos);
        if (strcmp(intval($uid), $uid) === 0) {
            $category = $cs->getCategory($uid);
        }

        if ($category === null) {
            throw new Bigace_Exception("News category does not exist");
        }

	    $id = Bigace_Config::get("news", "root.id");
	    $lang = Bigace_Config::get("news", "default.language");

	    if ($id === null) {
	        throw new Bigace_Exception('The News system is not configured. Please set configuration key: news/root.id');
		    return;
	    }

	    $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $id);
	    $ir->setLanguageID($lang);
	    $ir->setReturnType("Bigace_News_Item");
	    $ir->setOrderBy("valid_from");
	    $ir->setOrder('DESC');
	    if ($showHidden) {
		    $ir->addFlagToInclude(Bigace_Item_Request::HIDDEN);
    	}

    	// show only news of a special category
	    if ($category !== null) {
		    $ir->setCategory($category->getID());
	    }

	    $allNews = new Bigace_Item_Walker($ir);

	    $items = array();

	    import('classes.image.Image');

        foreach ($allNews as $news) {
            $image = null;

            if($news->getImageID() !== null)
                $image = new Image($news->getImageID());

		    $items[] = array(
		        'item' => $news,
		        'image' => $image
		    );
        }

	    $bns = new Bigace_News_Service();

	    $this->view->NEWS = $items;
	    $this->view->CATEGORY = $category;
	    //$this->view->CATEGORY_PAGES = $bns->getCategories();

	    $this->applyContent();
    }
}
