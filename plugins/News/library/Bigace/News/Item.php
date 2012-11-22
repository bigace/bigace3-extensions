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
 * @version    $Id: Item.php 4 2010-07-17 14:20:17Z kevin $
 */

/**
 * Class used for handling News.
 *
 * For currently used Text/Num/Date fields, see Item.php.
 *
 * @category   Bigace
 * @package    Bigace_News
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_News_Item extends Bigace_Item_Basic
{
    
    /**
     * Instantiates a News item with the given ID.
     * If you pass null as ID the Object will not be initialized but only
     * instantiated.
     *
     * @param int the Menu ID or null
     * @param String the treetype
     * @param String the Language ID
     */
    public function __construct($id = null, $language = null, $type = ITEM_LOAD_FULL)
    {
    	if(func_num_args() > 1 && $id != null && $language != null)
    	    parent::__construct(_BIGACE_ITEM_MENU, $id, $language, $type);
    }
    
    /**
     * Returns the News Teaser.
     *
     * @return String the teaser
     */
    function getTeaser()
    {
    	return $this->getDescription();
    }

    /**
     * Returns the News title
     *
     * @return String the title
     */
    function getTitle()
    {
    	return $this->getName();
    }

    /**
     * Returns the ID of the linked Image
     *
     * @return int the Image ID
     */
    function getImageID()
    {
    	return $this->getItemNum('1');
    }
    
    /**
     * Returns the News Date.
     * If not set, this returns the creation date.
     */
    function getDate()
    {
    	$d = $this->getValidFrom();
    	if($d === null || $d === 0)
    		return $this->getCreateDate();
    	return $d;
    }
}
