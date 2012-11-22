<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * This represents a single Guestbook Entry.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) 2002-2006 Kevin Papst
 * @version $Id: Guestbook.php 2 2011-01-31 13:29:57Z kevin $
 * @package bigace.classes
 * @subpackage guestbook
 */
class Bigace_Guestbook_Entry
{
	/**
	 * @var array
	 */
	private $entry = null;

	/**
	 * Gets the Entry for the given ID
	 *
	 * @param int the ID represeting the Entry
	 */
	public function __construct($id)
	{
	    $values = array('ID' => intval($id));
	    $sql = "SELECT * FROM {DB_PREFIX}gaestebuch WHERE id={ID} AND cid={CID}";
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
		$result = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
		$this->entry = $result->next();
	}


	/**
	 * Gets the entrys ID
	 *
	 * @return	String	the ID representing this entry
	 */
	public function getID()
	{
		return $this->entry["id"];
	}


	/**
	 * Gets the Name entry
	 *
	 * @return	String	the name entry
	 */
	public function getName()
	{
		return $this->entry["name"];
	}


	/**
	 * Gets the Email address for this entry
	 *
	 * @return	String	the Email address for this entry
	 */
	public function getEmail()
	{
		return $this->entry["email"];
	}


	/**
	 * Gets the Homepage URL for this entry
	 *
	 * @return	String	the Homepage URL for this entry
	 */
	public function getHomepage()
	{
		return $this->entry["homepage"];
	}


	/**
	 * Gets the Comment for this entry
	 *
	 * @return	String	the Comment for this entry
	 */
	public function getComment()
	{
		return $this->entry["eintrag"];
	}


	/**
	 * Gets the Date for this entry, normally the creation date
	 *
	 * @return	String	the Date for this entry
	 */
	public function getDate()
	{
		return $this->entry["timestamp"];
	}
}