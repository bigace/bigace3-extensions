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
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: CommentCounter.php 193 2011-04-11 09:30:02Z kevin $
 */

/**
 * Counts the amount of Comments for one Item.
 *
 * @category   Bigace
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Zend_View_Helper_CommentCounter extends Zend_View_Helper_Abstract
{
    /**
     * Returns the amount of comments for the given $item.
     *
     * @param Bigace_Item $item
     * @return integer
     */
    public function commentCounter(Bigace_Item $item)
    {
	    $table  = new Bigace_Comment_Table();
	    $select = $table->select(true)
                        ->columns('count(*) as counter')
                        ->where("itemid = ?", $item->getID())
                        ->where("language = ?", $item->getLanguageID())
                        ->where("itemtype = ?", $item->getItemTypeID())
                        ->where("activated = ?", 1)
                        ->group('itemid');

	    $row = $table->fetchRow($select);

        if ($row === null) {
            return 0;
        }

        return $row->counter;
    }

}
