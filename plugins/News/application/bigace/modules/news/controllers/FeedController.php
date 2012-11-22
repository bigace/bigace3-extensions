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
 * @version    $Id: FeedController.php 120 2011-03-15 15:10:20Z kevin $
 */

/**
 * Controller to render news feeds.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class News_FeedController extends Bigace_Zend_Controller_Action
{
    /**
     * make sure we news are configured
     */
    public function init()
    {
        parent::init();
	    $id = Bigace_Config::get("news", "root.id");
	    $lang = Bigace_Config::get("news", "default.language");

	    if ($id === null) {
	        throw new Bigace_Zend_Exception(
	           'The News system is not configured. Please set configuration key: news/root.id',
	           500
	        );
		    return;
	    }
    }

    /**
     * This script displays the latest created News.
     *
     * TODO make amount configurable
     * TODO make configurable whether feed is full or stripped
     */
    public function indexAction()
    {
        $this->getResponse()->setHeader("Content-Type", 'application/rss+xml', true);

        $id   = Bigace_Config::get("news", "root.id");
        $lang = Bigace_Config::get("news", "default.language");

        $ir = new Bigace_Item_Request(_BIGACE_ITEM_MENU, $id);
        $ir->setLanguageID($lang)
           ->setReturnType("Bigace_News_Item")
           ->setOrderBy("valid_from")
           ->setOrder('DESC')
           ->setLimit(0, 10);

        $this->view->NEWS = new Bigace_Item_Walker($ir);

        // for correct date formatting required!
        setlocale(LC_TIME, "en_EN");

        $type = $this->getRequest()->getParam('type', null);

        switch($type) {
            case Zend_Feed_Writer::TYPE_ATOM_ANY:
            case Zend_Feed_Writer::TYPE_RSS_ANY:
                break;
        	default:
        		$type = Zend_Feed_Writer::TYPE_ATOM_ANY;
        		break;
        }
        $url = 'news/feed/index/';
        if ($type !== null) {
            $url .= $type . '/';
        }

        $this->view->FEED_FULL = false;
        $this->view->FEED_URL  = LinkHelper::url($url);
        $this->view->FEED_TYPE = $type;
    }

    /**
     * This script displays the latest created comments.
     * If none item was passed, all comments will be considered.
     * Otherwise we display the latest comments of a special item.
     */
    public function commentsAction()
    {
        $this->getResponse()->setHeader("Content-Type", 'application/rss+xml', true);

        $service = new Bigace_Comment_Service();
        $this->view->COMMENTS = $service->getLatestComments(null, 10);

        // for correct date formatting required!
        setlocale(LC_TIME, "en_EN");

        $type = $this->getRequest()->getParam('type', null);

        switch($type) {
            case Zend_Feed_Writer::TYPE_ATOM_ANY:
            case Zend_Feed_Writer::TYPE_RSS_ANY:
                break;
            default:
                $type = Zend_Feed_Writer::TYPE_ATOM_ANY;
                break;
        }
        $url = 'news/feed/comments/';
        if ($type !== null) {
            $url .= $type . '/';
        }

        $this->view->FEED_URL  = LinkHelper::url($url);
        $this->view->FEED_TYPE = $type;
    }

}