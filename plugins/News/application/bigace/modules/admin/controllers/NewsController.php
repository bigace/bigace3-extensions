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
 * @version    $Id: NewsController.php 193 2011-04-11 09:30:02Z kevin $
 */

/**
 * Script for News management.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_NewsController extends Bigace_Zend_Controller_Admin_Action
{
    private $_rootID = null;
    private $_categoryID = null;
    private $_newsLang = null;

    private $_canEdit = null;
    private $_canDelete = null;
    private $_canCreate = null;
    private $_canConfig = null;

    public function initAdmin()
    {
        if (!defined('NEWS_CTRL')) {
            $this->addTranslation('news');

            import('classes.category.ItemCategoryEnumeration');
            import('classes.category.CategoryAdminService');
            import('classes.image.Image');

            define('NEWS_CTRL', true);
        }

        if ($this->_rootID === null) {
            $this->_rootID = Bigace_Config::get("news", "root.id");
            $this->_categoryID = Bigace_Config::get("news", "category.id");
            $this->_newsLang = Bigace_Config::get("news", "default.language");

            $this->_canEdit = has_permission('news');
            $this->_canCreate = $this->_canEdit;
            $this->_canDelete = $this->_canEdit;
            $this->_canConfig = has_permission('admin_configurations');
        }
    }

    /**
     * List existing news.
     */
    public function indexAction()
    {
        $missing = array();

        if($this->_rootID === null)     $missing[] = 'root.id';
        if($this->_categoryID === null) $missing[] = 'category.id';
        if($this->_newsLang === null) 	$missing[] = 'default.language';

        if (count($missing) > 0) {
            $this->view->CONFIG_URL = $this->createLink('configurations', 'index');
            $this->view->MISSING = $missing;
            $this->render('unconfigured');
            return;
        }

	    $this->view->CREATE_URL = $this->createLink('news', 'create');

	    $this->view->PERM_CREATE = $this->_canCreate;
	    $this->view->PERM_DELETE = $this->_canDelete;
	    $this->view->PERM_EDIT = $this->_canEdit;
	    $this->view->PERM_CONFIG = $this->_canConfig;

	    $this->view->CATEGORIES = '';
	    $this->view->LIMIT = 0;

	    $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $this->_rootID);
	    $ir->setLanguageID($this->_newsLang)
	       ->setReturnType("Bigace_News_Item")
	       ->setOrderBy("valid_from")
	       ->setOrder('DESC')
	       ->addFlagToInclude(Bigace_Item_Request::HIDDEN)
	       ->ignoreValidFrom();

	    $archiveTs = 0;
	    if (!empty($_POST['ts']) && intval($_POST['ts']) == $_POST['ts']) {
	        // show the complete archive of the choosen month
    	    $archiveTs = intval($_POST['ts']);
    	    $lastTS = strtotime("+1 month", $archiveTs);

    	    $ir->where('a.valid_from >= '.$archiveTs)
    	       ->where('a.valid_from <= '.$lastTS);
	    } else {
	        $ir->setLimit(0, 20);
	    }

	    $menuInfo = new Bigace_Item_Walker($ir);

	    $items = array();
	    $uitems = array();

        for ($i=0; $i < $menuInfo->count(); $i++) {
            $temp = $menuInfo->next();
            $temp2 = array(
		        'item'   => $temp,
		        'edit'   => $this->createLink(
                    'news',
                    'edit',
                    array('newsID' => $temp->getID(), 'newsLangID' => $temp->getLanguageID())
                ),
		        'delete' => $this->createLink(
                    'news',
                    'delete',
                    array('newsID' => $temp->getID(), 'newsLangID' => $temp->getLanguageID())
                ),
		    );

            if ($temp->getDate() > time()) {
    		    $uitems[] = $temp2;
    		} else {
    		    $items[] = $temp2;
    		}
        }

        $service = new Bigace_News_Service();
        $archives = $service->getArchives($this->_newsLang);

	    $this->view->ARCHIVE_URL = $this->createLink('news');
	    $this->view->ARCHIVE_TS = $archiveTs;
	    $this->view->ARCHIVES = $archives;
	    $this->view->UNPUBLISHED = $uitems;
	    $this->view->NEWS = $items;
    }

    /**
     * Display formular to create new entry.
     */
    public function createAction()
    {
        if (!$this->_canCreate) {
            $this->view->ERROR = getTranslation('missing_permission');
            $this->_forward('index');
            return;
        }

        $content = (isset($_POST['newsContent']) ? $_POST['newsContent'] : '');
        $teaser = (isset($_POST['teaser']) ? $_POST['teaser'] : '');
        $title = (isset($_POST['title']) ? $_POST['title'] : '');
        $categories = (isset($_POST['categories']) ? $_POST['categories'] : array());
        $newCategories = (isset($_POST['newCategories']) ? $_POST['newCategories'] : '');
        $image = null;

        if (isset($_POST['imageID']) && strlen(trim($_POST['imageID'])) > 0) {
            $image2 = new Image($_POST['imageID']);
            if($image2->exists())
                $image = $image2;
        }

        // formular parameter
	    $vals = array(
		    'hiddenValues'	=> array('newsLangID' => $this->_newsLang),
		    'image'		    => $image,
		    'newsDate'		=> mktime(date("G"), 0, 0, date("m"), date("d"), date("Y")),
		    'content'		=> $content,
		    'teaser'		=> $teaser,
		    'title'			=> $title,
		    'mode'			=> 'save',
		    'newCategories'	=> $newCategories,
		    'categories'	=> $categories,
		    'submit_url'    => $this->createLink('news', 'save')
	    );

	    $allMeta = Bigace_Hooks::apply_filters('create_item_meta', array(), _BIGACE_ITEM_MENU);
        if (is_array($allMeta) && count($allMeta) > 0) {
            $vals['meta'] = $allMeta;
        }

        $this->newsEdit($vals, $this->createLink('news'));
    }

	/**
	 * Create a new News entry.
	 */
    public function saveAction()
    {
        if (!$this->_canCreate) {
            $this->view->ERROR = getTranslation('missing_permission');
            $this->_forward('index');
            return;
        }

        $itemLangID = $this->getRequest()->getParam('newsLangID');

        if ($itemLangID === null) {
	        $this->_forward('index');
	        $this->view->ERROR = getTranslation('missing_values');
	        return;
        }

        if (empty($_POST['title'])) {
            $this->view->ERROR = getTranslation('error_title');
	        $this->_forward('create');
            return;
        }

        $newCats = (isset($_POST['categories']) ? $_POST['categories'] : array());

        // always assign the news root id
        $newCats[] = $this->_categoryID;

        // create requested categories too
        if (isset($_POST['newCategories']) && strlen(trim($_POST['newCategories'])) > 0) {
	        $newCats = array_merge($newCats, $this->createNewCategories($_POST['newCategories']));
        }

        $imgID     = ((isset($_POST['imageID']) && strlen(trim($_POST['imageID'])) > 0) ? $_POST['imageID'] : null);
        $hour      = intval($_POST['newsDateHour']);
        $minute    = intval($_POST['newsDateMinute']);
        $date      = strtotime($_POST['newsDate']);
        $publishTS = mktime($hour, $minute, 0, date("n", $date), date("j", $date), date("Y", $date));

        $nas = new Bigace_News_Service();
        $id = $nas->createNews(
            $_POST['newsLangID'],
	        Bigace_Util_Sanitize::plaintext($_POST['title']),
	        Bigace_Util_Sanitize::html($_POST['teaser']),
	        '',
	        Bigace_Util_Sanitize::html($_POST['newsContent']),
	        $newCats,
	        strtotime($_POST['newsDate']),
	        $imgID,
	        $publishTS
	    );

        if ($id === false) {
            $this->view->ERROR = getTranslation('error_creating');
            $this->_forward('create');
        } else {
	        $news = new Bigace_News_Item($id, $_POST['newsLangID']);
	        $link = LinkHelper::getCMSLinkFromItem($news);
	        $this->view->INFO = getTranslation('news_created');
            $this->_forward('index');
        }
    }


    /**
     * Open formular to edit existing entry
     */
    public function editAction()
    {
        $itemID = $this->getRequest()->getParam('newsID');
        $itemLangID = $this->getRequest()->getParam('newsLangID');

        if ($itemID === null || $itemLangID === null) {
	        $this->_forward('index');
	        $this->view->ERROR = getTranslation('missing_values');
	        return;
        }

        $news = new Bigace_News_Item($itemID, $itemLangID);

        $categories = array();

        $icenum = new ItemCategoryEnumeration(_BIGACE_ITEM_MENU, $itemID);
        while ($icenum->hasNext()) {
            $temp = $icenum->next();
            $categories[] = $temp->getID();
        }

        $type = Bigace_Item_Type_Registry::get(_BIGACE_ITEM_MENU);
        $cs   = $type->getContentService();
        $cnt  = $cs->get($news, $cs->query());

        $vals = array(
            'hiddenValues'	=> array('newsID' => $itemID, 'newsLangID' => $itemLangID),
            'image'	    	=> null,
            'newsDate'		=> $news->getDate(),
            'content'		=> $cnt->getContent(),
            'teaser'		=> $news->getTeaser(),
            'title'			=> $news->getTitle(),
            'mode'			=> 'update',
            'categories'	=> $categories,
		    'newCategories'	=> '',
		    'submit_url'    => $this->createLink('news', 'update')
        );

        $allMeta = Bigace_Hooks::apply_filters('edit_item_meta', array(), $news);
        if (is_array($allMeta) && count($allMeta) > 0) {
            $vals['meta'] = $allMeta;
        }

        if (!is_null($news->getImageID())) {
            $image = new Image($news->getImageID());
            if($image->exists())
                $vals['image'] = $image;
        }

        $this->newsEdit($vals);
    }

    /**
     * Show formular to delete existing entry.
     */
    public function deleteAction()
    {
        if (!$this->_canDelete) {
            $this->view->ERROR = getTranslation('missing_permission');
            $this->_forward('index');
            return;
        }

        $itemID = $this->getRequest()->getParam('newsID');
        $itemLangID = $this->getRequest()->getParam('newsLangID');

        if ($itemID === null || $itemLangID === null) {
            $this->view->ERROR = getTranslation('missing_values');
            $this->_forward('index');
            return;
        }

        $news = new Bigace_News_Item($itemID, $itemLangID);
        $this->view->NEWS = $news;
        $this->view->ACTION = $this->createLink('news', 'remove');
        $this->view->BACK_URL = $this->createLink('news');
    }

    /**
     * Saves/Updates an edited (and existing) entry.
     */
    public function updateAction()
    {
        if (!$this->_canEdit) {
            $this->view->ERROR = getTranslation('missing_permission');
            $this->_forward('index');
            return;
        }

        $itemID = $this->getRequest()->getParam('newsID');
        $itemLangID = $this->getRequest()->getParam('newsLangID');

        if ($itemID === null || $itemLangID === null) {
	        $this->_forward('index');
	        $this->view->ERROR = getTranslation('missing_values');
	        return;
        }

        $this->_forward('edit');

        // save/update existing entry
        if (!isset($_POST['title'])) {
            $this->view->ERROR = getTranslation('error_title');
            return;
        } else {
	        $newCats = (isset($_POST['categories']) ? $_POST['categories'] : array());

	        // always assign the news root id
	        $newCats[] = $this->_categoryID;

	        // create requested categories too
	        if (isset($_POST['newCategories']) && strlen(trim($_POST['newCategories'])) > 0) {
		        $newCats = array_merge($newCats, $this->createNewCategories($_POST['newCategories']));
	        }

            $hour = intval($_POST['newsDateHour']);
            $minute = intval($_POST['newsDateMinute']);
            $date = strtotime($_POST['newsDate']);
            $publishTS = mktime($hour, $minute, 0, date("n", $date), date("j", $date), date("Y", $date));

	        $nas = new Bigace_News_Service();
	        $rr = $nas->updateNews(
                $_POST['newsID'],
                $_POST['newsLangID'],
                Bigace_Util_Sanitize::plaintext($_POST['title']),
                Bigace_Util_Sanitize::html($_POST['teaser']),
                '',
                Bigace_Util_Sanitize::html($_POST['newsContent']),
                $newCats,
                strtotime($_POST['newsDate']),
                $_POST['imageID'],
                $publishTS
            );

	        if ($rr === FALSE) {
                $this->view->ERROR = getTranslation('error_updating');
                $this->_forward('edit');
	        } else {
		        $news = new Bigace_News_Item($_POST['newsID'], $_POST['newsLangID']);
		        $link = LinkHelper::getCMSLinkFromItem($news);
                $this->view->INFO = getTranslation('news_saved');
	        }
        }
    }

    /**
     * delete existing entry
     */
    public function removeAction()
    {
        $this->_forward('index');

        if (!$this->_canDelete) {
            $this->view->ERROR = getTranslation('missing_permission');
            return;
        }

        $itemID = $this->getRequest()->getParam('newsID');
        $itemLangID = $this->getRequest()->getParam('newsLangID');

        if ($itemID === null || $itemLangID === null) {
	        $this->view->ERROR = getTranslation('missing_values');
	        return;
        }

        $nas = new Bigace_News_Service();

        if ($nas->deleteNews($itemID, $itemLangID)) {
            $this->view->INFO = getTranslation('news_deleted');
        } else {
	        $this->view->ERROR = getTranslation('error_deleting');
        }
    }

    /**
     * Creates new Categories from the given String.
     * The String may contain multiple Category names, divided by a comma.
     */
    function createNewCategories($categories)
    {
	    $catIDs = array();
	    $cats = explode(",", $categories);
	    if (count($cats) > 0) {
		    $cas = new CategoryAdminService();
		    foreach ($cats as $cc) {
		        $newName = trim($cc);
		        if (strlen($newName) > 0) {
			        $temp = $cas->createCategory(
                        $this->_categoryID, $newName, getTranslation('news_category_default_description')
                    );
			        if ($temp === false) {
			            $this->view->ERROR = "Could not create category: " . $newName;
			        } else {
				        $catIDs[] = $temp;
    			    }
			    }
		    }
	    }
	    return $catIDs;
    }

    /**
     * Shows the formular to edit a news entry.
     * This form is used for both: new AND existing news
     */
    function newsEdit($newsEditArray, $backUrl = null)
    {
        if($backUrl === null)
            $backUrl = $this->createLink('news');

        $this->view->BACK_URL = $backUrl;

	    $this->view->EDIT_CONFIG = $newsEditArray;

	    $this->view->NEWS_ROOT_CATEGORY = $this->_categoryID;
	    $this->view->NEWS_CATEGORIES = $newsEditArray['categories'];

	    $this->view->PERM_CREATE = $this->_canCreate;
	    $this->view->PERM_DELETE = $this->_canDelete;
	    $this->view->PERM_EDIT = $this->_canEdit;
	    $this->view->PERM_CONFIG = $this->_canConfig;

	    $this->render('edit');
    }

}
