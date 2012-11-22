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
 * @package    Bigace_News
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Service.php 92 2011-03-15 10:02:44Z kevin $
 */

/**
 * Class used for administrating "News" entrys.
 *
 * @category   Bigace
 * @package    Bigace_News
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_News_Service
{

    public function __construct()
    {
    }

    public function getNewsRootID()
    {
        return Bigace_Config::get("news", "root.id");
    }

    /**
     * @access private
     */
    private function prepareNewsDataArray($language, $title, $teaser, $metatags, $content,
        $newsDate = null, $imageID = null, $publishDate = null)
    {
    	if ($newsDate === null)
    	   $newsDate = time();

    	if (strlen(trim($imageID)) == 0) {
    	    $imageID = null;
    	}

    	$vals =  array(
    		'langid'		=> $language,
    		'name'			=> $title,
    		'type'			=> 'news',
    		'description'	=> $teaser,
    		'catchwords'	=> $metatags,
    		'content'		=> $content,
    		'date_2'		=> $newsDate,
    		'num_1'			=> $imageID,
    		'valid_from'    => $publishDate,
    		'num_3'			=> FLAG_NORMAL,
    		'parentid'		=> $this->getNewsRootID(),
            'mimetype'      => 'text/html'
    	);

		return $vals;
    }

    /**
     * Deletes a News entry with the given ID and Language.
     *
     * @param int the News ID
     * @param String the Language locale
     */
    public function deleteNews($id, $language)
    {
        import('classes.item.ItemAdminService');

        $mas   = new ItemAdminService(_BIGACE_ITEM_MENU);
        $item  = $mas->getClass($id, ITEM_LOAD_FULL, $language);
        $admin = new Bigace_Item_Admin();
        return $admin->delete($item);
    }

    private function getModel($id, $language)
    {
        import('classes.item.ItemAdminService');

        $mas = new ItemAdminService(_BIGACE_ITEM_MENU);
        $item = $mas->getClass($id, ITEM_LOAD_FULL, $language);
        $model = new Bigace_Item_Admin_Model($item);
        $model->mimetype = 'text/html';
        return $model;
    }

    /**
     * Updates a News entry with the given values.
     *
     * @return true on success, false on error
     */
    public function updateNews($id, $language, $title, $teaser, $metatags, $content, $categories = array(),
        $newsDate = null, $imageID = null, $publishDate = null)
    {
        import('classes.item.ItemAdminService');

        $model = $this->getModel($id, $language);

    	$data = $this->prepareNewsDataArray(
    	    $language, $title, $teaser, $metatags, $content, $newsDate, $imageID, $publishDate
    	);

        $admin = new Bigace_Item_Admin();
        $model->setArray($data);
        $item = $admin->save($model);

    	$catRoot = Bigace_Config::get("news", "category.id");

        import('classes.category.CategoryAdminService');
        $cas = new CategoryAdminService();
        $cas->deleteLinksForItem(_BIGACE_ITEM_MENU, $id);

        // make sure news root category is linked
    	if (!in_array($catRoot, $categories)) {
    	    $categories = array_merge($categories, array($catRoot));
	    }

		// create all category links new
        if (count($categories) > 0) {
	    	foreach ($categories as $catID) {
	    		$cas->createCategoryLink(_BIGACE_ITEM_MENU, $id, $catID);
	    	}
    	}

    	$type = Bigace_Item_Type_Registry::get(_BIGACE_ITEM_MENU);
    	$cs   = $type->getContentService();
    	$cnt  = $cs->create()->setContent($content);

    	return $cs->save($item, $cnt);
    }

	/**
     * Creates a News entry with the given values.
     *
     * @return integer
     */
    public function createNews($language, $title, $teaser, $metatags, $content, $categories = array(),
        $newsDate = null, $imageID = null, $publishDate = null)
    {
        import('classes.item.ItemAdminService');

    	$data = $this->prepareNewsDataArray(
    	    $language, $title, $teaser, $metatags, $content, $newsDate, $imageID, $publishDate
    	);

    	$pattern   = Bigace_Config::get('news', 'unique.name.pattern', '');
    	$extension = Bigace_Config::get('news', 'unique.name.extension', '');
    	$catRoot   = Bigace_Config::get("news", "category.id");
    	$pattern   = strftime($pattern);

        $mas = new ItemAdminService(_BIGACE_ITEM_MENU);
    	$data['unique_name'] = $mas->buildUniqueNameSafe($pattern.$data['name'].$extension, $extension);

        $admin = new Bigace_Item_Admin();
        $model = new Bigace_Item_Admin_Model($data);
        $model->itemtype = _BIGACE_ITEM_MENU;
        $item = $admin->save($model);

    	if ($item === null) {
    		return false;
    	}

        // make sure news root category is linked
    	if (!in_array($catRoot, $categories)) {
    	    $categories = array_merge($categories, array($catRoot));
	    }

    	if (count($categories) > 0) {
            import('classes.category.CategoryAdminService');
	    	$cs = new CategoryAdminService();
	    	foreach ($categories as $catID) {
	    		$cs->createCategoryLink(_BIGACE_ITEM_MENU, $item->getID(), $catID);
	    	}
    	}

        $type = Bigace_Item_Type_Registry::get(_BIGACE_ITEM_MENU);
        $cs   = $type->getContentService();
        $cnt  = $cs->create()->setContent($content);
        $cs->save($item, $cnt);

    	return $item->getID();
    }

    /**
     * Returns all available News archives for the given $language.
     *
     * @param string $language
     * @return array(Bigace_News_Archive)
     */
    public function getArchives($language)
    {
	    $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $this->getNewsRootID());
	    $ir->setLanguageID($language)
	       ->select("FROM_UNIXTIME(a.valid_from, '%Y%m') AS ym")
	       ->groupBy("ym DESC");

        $all = Bigace_Item_Requests::countItems($ir);

        $archives = array();
        foreach ($all as $t) {
            $year = substr($t["ym"], 0, 4);
            $month = substr($t["ym"], 4, 2);
            $archives[] = new Bigace_News_Archive($year, $month, $t["amount"]);
        }

        return $archives;
    }

    /**
     * Returns the X latest news.
     *
     * If you want a special amount use th $amount parameter (otherwised the configured
     * amount for the news Homepage are taken). If you want to display News of a special
     * category, pass an array with Category IDs in $category.
     *
     * @param integer $amount
     * @param string $language
     * @param array $category
     * @return Bigace_Item_Walker
     */
    public function getLatest($amount = null, $language = null, $category = array())
    {
        $id   = Bigace_Config::get("news", "root.id");
        $lang = $language !== null ? $language : Bigace_Config::get("news", "default.language");
        $to   = $amount !== null ? $amount : Bigace_Config::get("news", "homepage.amount", 10);

        if ($id === null) {
            throw new Bigace_Exception(
               'The News system is not configured. Please set configuration key: news/root.id'
            );
            return;
        }

        $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $id);
        $ir->setLanguageID($lang)
           ->setReturnType("Bigace_News_Item")
           ->setOrderBy("valid_from")
           ->setOrder('DESC');

        if ($to !== null && $to != 0) {
            $ir->setLimit(0, $to);
        }

        // show only news of one or more categories
        if (count($category) > 0) {
            foreach ($category as $catId) {
                $ir->setCategory($catId);
            }
        }

        return new Bigace_Item_Walker($ir);
    }

}