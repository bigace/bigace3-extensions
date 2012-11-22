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
 * Adminservice for your Guestbook, holding all functions for write access.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id: GuestbookAdminService.php 2 2011-01-31 13:29:57Z kevin $
 * @package bigace.classes
 * @subpackage guestbook
 */
class Bigace_Guestbook_Service
{

    /**
     * Creates a new Guestbook Entry
     *
     * @param    String  the Visitors Name
     * @param    String  the Visitors EMail Adress
     * @param    String  the Visitors Homepage Adress
     * @param    String  the Visitors Comment
     * @return   int     the new generated Guestbook Entry ID
     */
    public function createEntry($name, $email, $homepage, $comment)
    {
	    $values = array( 'NAME'         => $name,
	                     'EMAIL'        => $email,
	                     'HOMEPAGE'     => $homepage,
	                     'COMMENT'      => strip_tags($comment),
	                     'TIMESTAMP'    => time() );

        $sql = "INSERT INTO  {DB_PREFIX}gaestebuch (cid, name, email, homepage, eintrag, timestamp)".
               " VALUES ({CID}, {NAME}, {EMAIL}, {HOMEPAGE}, {COMMENT}, {TIMESTAMP})";
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sql);
    }

    /**
     * This changes a single Guestbook Entry
     *
     * @param    int     the Entry ID to change
     * @param    String  the changed Name
     * @param    String  the changed Email Adress
     * @param    String  the changed Homerpage Adress
     * @param    String  the changed Comment
     * @param    Object  the changed Date
     * @return   Object  the DB Result for this UPDATE
     */
    public function changeEntry($id, $name, $email, $homepage, $comment)
    {
	    $values = array( 'ENTRY_ID'     => $id,
	                     'NAME'         => $name,
	                     'EMAIL'        => $email,
	                     'HOMEPAGE'     => $homepage,
	                     'COMMENT'      => $comment );
        $sql = "UPDATE {DB_PREFIX}gaestebuch SET name={NAME}, email={EMAIL}, homepage={HOMEPAGE}, " .
               "eintrag={COMMENT} WHERE id={ENTRY_ID} AND cid={CID}";
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

    /**
     * Deletes a GuestbookEntry
     *
     * @param    int     the GuestbookEntry ID
     * @return   Object  the DB Result for this DELETE
     */
    public function deleteEntry($id)
    {
	    $values = array('ID' => intval($id));
        $sql = "DELETE FROM {DB_PREFIX}gaestebuch WHERE id={ENTRY_ID} AND cid={CID}";
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

}