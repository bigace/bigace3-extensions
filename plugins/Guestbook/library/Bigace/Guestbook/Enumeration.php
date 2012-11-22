<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @package bigace.classes
 * @subpackage guestbook
 */

/**
 * Can be used to receive all or a list of Guestbook Entrys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id: GuestbookEnumeration.php 2 2011-01-31 13:29:57Z kevin $
 * @package bigace.classes
 * @subpackage guestbook
 */
class Bigace_Guestbook_Enumeration
{
	/**
	 * @var array
	 */
	private $all;

	public function __construct($from = 0, $limit = 1000)
	{
        $sql = "SELECT id FROM {DB_PREFIX}gaestebuch WHERE cid={CID} ORDER BY timestamp DESC LIMIT";
        $sql .= " " . intval($from) . ", " . intval($limit);
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array(), true);
		$this->all = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
	}

	public function getAllEntrys()
	{
		return $this->all;
	}

	public function getNextEntry()
	{
		$temp = $this->all->next();
		return new Bigace_Guestbook_Entry($temp["id"]);
	}

	public function countEntrys()
	{
	    if (is_object($this->all)) {
    		return $this->all->count();
	    }
        return 0;
	}

	/**
	 * @return integer
	 */
	public function countAllEntrys()
	{
	    $sql = "SELECT COUNT(id) as amount FROM {DB_PREFIX}gaestebuch WHERE cid={CID}";
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array(), true);
		$entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
		$temp = $entrys->next();
		return $temp['amount'];
	}

	public function count()
	{
		return $this->countEntrys();
	}

	public function next()
	{
		return $this->getNextEntry();
	}

}