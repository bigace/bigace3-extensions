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
 * @version    $Id: Service.php 117 2011-03-15 13:13:43Z kevin $
 */

/**
 * Helper functions for handling comments.
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Comment_Service
{
    private static $activated = false;
    protected $dbTable;

    /**
     * Returns whether the comment system is activated or not.
     * @return boolean
     */
    public static function isActivated()
    {
        return self::$activated;
    }

    /**
     * Sets whether the comment system is activated.
     * @param boolean $active
     */
    public static function setActivated($active)
    {
        if ($active != null && is_bool($active))
            self::$activated = $active;
    }

    /**
     *
     * @param $dbTable
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Bigace_Db_Table_Abstract) {
            throw new Bigace_Events_Exception('Invalid table data gateway provided');
        }
        $this->dbTable = $dbTable;
        return $this;
    }

    /**
     * @return Bigace_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->dbTable) {
            $this->setDbTable(new Bigace_Comment_Table());
        }
        return $this->dbTable;
    }

    public function getComments(Bigace_Item $item)
    {
        $table = $this->getDbTable();
        $select = $table->select()
                ->where("itemtype = ?", $item->getItemType())
                ->where("itemid = ?", $item->getID())
                ->where("language = ?", $item->getLanguageID())
                ->where("activated = ?", 1)
                ->order('timestamp ASC');
        $rows = $table->fetchAll($select);

        if ($rows === null) {
            return null;
        }

        return $this->resultToArray($rows);
    }

    public function getComment($id)
    {
        $table = $this->getDbTable();
        $row = $table->fetchRow(
            $table->select()->where("id = ?", $id)
        );

        if ($row === null) {
            return null;
        }

        return $this->rowToEntry($row);
    }
    /**
     * If $item is not set, returns the latest comments from the community,
     * otherwise of the $item.
     * @param Bigace_Item $item
     */
    public function getLatestComments(Bigace_Item $item = null, $amount = 10)
    {
        $table = $this->getDbTable();
        $select = $table->select()
                ->where("activated = ?", 1)
                ->order('timestamp DESC')
                ->limit(0, $amount);

        if ($item !== null) {
            $select->where("itemtype = ?", $item->getItemType())
                ->where("itemid = ?", $item->getID())
                ->where("language = ?", $item->getLanguageID());
        }

        $rows = $table->fetchAll($select);

        if ($rows === null) {
            return null;
        }

        return $this->resultToArray($rows);
    }

    private function rowToEntry($row)
    {
        $comment = new Bigace_Comment_Entry();
        $comment->setId($row->id)
                ->setItemtype($row->itemtype)
                ->setItemid($row->itemid)
                ->setLanguage($row->language)
                ->setName($row->name)
                ->setEmail($row->email)
                ->setHomepage($row->homepage)
                ->setIp($row->ip)
                ->setComment($row->comment)
                ->setTimestamp($row->timestamp)
                ->setActivated($row->activated)
                ->setAnonymous($row->anonymous)
                ->setType($row->type);
        return $comment;
    }

    private function resultToArray($result)
    {
        $entries = array();
        foreach ($result as $row) {
            $comment = $this->rowToEntry($row);
            $entries[] = $comment;
        }
        return $entries;
    }


    /**
     * Check if trackbacks or pings are allowed for this item.
     */
    public function activeRemoteComments(Bigace_Item $item)
    {
        $ips = new Bigace_Item_Project_Numeric();
        $active = $ips->get($item, 'allow_trackbacks');
        if ($active === null)
            return true;
        return (bool)$active;
    }

    /**
     * Check if comments are allowed for this item.
     */
    public function activeComments(Bigace_Item $item)
    {
        $ips = new Bigace_Item_Project_Numeric();
        $active = $ips->get($item, 'allow_comments');
        if ($active === null)
            return true;
        return (bool)$active;
    }


    /**
     * Counts the amount of activated comments.
     * @return int
     */
    public function countActivated()
    {
        $table  = $this->getDbTable();
        $select = $table->select(true)
                        ->reset(Zend_Db_Select::COLUMNS)
                        ->columns(new Zend_Db_Expr('count(id) as amount'))
                        ->where("activated = ?", 1);
        $row = $table->fetchRow($select);

        if ($row === null) {
            return null;
        }

        return $row->amount;
    }

    /**
     * Counts the amount of pending comments.
     * @return int
     */
    public function countPending()
    {
        $table  = $this->getDbTable();
        $select = $table->select(true)
                        ->reset(Zend_Db_Select::COLUMNS)
                        ->columns(new Zend_Db_Expr('count(id) as amount'))
                        ->where("activated = ?", 0);
        $row = $table->fetchRow($select);

        if ($row === null) {
            return null;
        }

        return $row->amount;
    }

    // =======================================================================
    // TODO Change methods to use Bigace_Db_Table
    // =======================================================================

    /**
     * Counts the amount of comments marked as spam.
     * @return int
     */
    public function countSpam()
    {
        $values = array();
        $sqlString = "SELECT `counter` FROM {DB_PREFIX}comment_spam_counter WHERE `cid` = {CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array(), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $res = $res->next();
        return $res['counter'];
    }

    /**
     * Checks if there is a ping or trackback from that adress, ignoring the
     * content of the comment.
     *
     * @param integer $itemtype
     * @param integer $id
     * @param string $language
     * @param string $url
     * @return boolean
     */
    public function checkRemoteCommentDuplicate($itemtype, $id, $language, $url)
    {
        $values = array('ITEMTYPE' => $itemtype, 'ID' => $id, 'LANGUAGE' => $language, 'URL' => $url);
        $sql = "SELECT * FROM {DB_PREFIX}comments WHERE `cid` = {CID} AND
                `itemtype` = {ITEMTYPE} AND `itemid` = {ID} AND `language` = {LANGUAGE}
                AND (`type` = 'trackback' OR `type` = 'ping') AND `homepage` = {URL}";
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        if ($res->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Checks if there is a comment from that email adress with
     * username and the comment for the item.
     *
     * @param Bigace_Item $item
     * @param string $name
     * @param string $content
     * @return boolean
     */
    public function checkCommentDuplicate(Bigace_Item $item, $name, $content)
    {
        $values = array(
            'ITEMTYPE' => $item->getItemtypeId(), 'ID' => $item->getID(),
            'LANGUAGE' => $item->getLanguageID(), 'NAME' => $name,
            'COMMENT' => $content
        );
        $sql = "SELECT * FROM {DB_PREFIX}comments WHERE `cid` = {CID} AND
                `itemtype` = {ITEMTYPE} AND `itemid` = {ID} AND `language` = {LANGUAGE}
                AND `type` = 'comment' AND `name` = {NAME} AND `comment` = {COMMENT}";
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        if ($res->count() > 0) {
            return true;
        }

        return false;
    }

}
