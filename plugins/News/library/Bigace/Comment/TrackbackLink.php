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
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: TrackbackLink.php 4 2010-07-17 14:20:17Z kevin $
 */

import('classes.util.CMSLink');

/**
 * This class generates a link to trackback one item.
 * 
 * FIXME doesn't work yet
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Comment_TrackbackLink extends CMSLink
{
    
    function __construct($item = null)
    {
        $this->setCommand('trackback');
        if(!is_null($item))
        	$this->setItem($item);
    }
    
    function setItem($item)
    {
    	$this->setItemID($item->getID());
    	$this->setLanguageID($item->getLanguageID());
    	if($item->getItemtypeID() != _BIGACE_ITEM_MENU)
    		$this->addParameter('itemtype', $item->getItemtypeID());
    }
}
