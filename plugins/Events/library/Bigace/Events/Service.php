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
 * @package    Bigace_Events
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Service.php 108 2011-03-15 10:20:55Z kevin $
 */

/**
 * Read and write events.
 *
 * @category   Bigace
 * @package    Bigace_Events
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Events_Service
{
    protected $dbTable;

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

    public function getDbTable()
    {
        if (null === $this->dbTable) {
            $this->setDbTable(new Bigace_Events_Table());
        }
        return $this->dbTable;
    }

    public function save(Bigace_Events_Event $event)
    {
        $data = array(
            'id' => $event->getId(),
            'type' => $event->getType(),
            'details' => $event->getDetails(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'location' => $event->getLocation(),
            'artist' => $event->getArtist(),
            'startdate' => $event->getStartDate(),
            'enddate' => $event->getEndDate(),
        );

        if (null === ($id = $event->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function get($id)
    {
        $table = $this->getDbTable();
        $row = $table->fetchRow(
            $table->select()->where("id = ?", $url)
        );
        if ($row === null) {
            return null;
        }
        $event = new Bigace_Events_Event();
        $event->setId($row->id)
              ->setTitle($row->title)
              ->setArtist($row->artist)
              ->setLocation($row->location)
              ->setType($row->type)
              ->setDetails($row->details)
              ->setDescription($row->description)
              ->setStartDate($row->startdate)
              ->setEndDate($row->enddate);
        return $event;
    }

    public function getLatest($amount, $type = null, $start = null)
    {
        if ($start === null) {
            $start = time();
        }

        $table  = $this->getDbTable();
        $select = $table->select()
                        ->where("startdate > ?", $start)
                        ->order("startdate DESC")
                        ->limit($amount);

        $row = $table->fetchRow($select);

        if ($row === null) {
            return null;
        }

        return $this->resultToArray($row);
    }

    public function getAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        return $this->resultToArray($resultSet);
    }

    private function resultToArray($result)
    {
        $entries = array();
        foreach ($result as $row) {
            $event = new Bigace_Events_Event();
            $event->setId($row->id)
                  ->setTitle($row->title)
                  ->setArtist($row->artist)
                  ->setLocation($row->location)
                  ->setType($row->type)
                  ->setDetails($row->details)
                  ->setDescription($row->description)
                  ->setStartDate($row->startdate)
                  ->setEndDate($row->enddate);
            $entries[] = $event;
        }
        return $entries;
    }

    /*
    public function getArchives()
    {
        throw new Bigace_Events_Exception('Method getArchives() not implemented');
        $table = $this->getDbTable();
        $resultSet = $table->fetchAll(
            $table->select()
                  ->from($table, array("FROM_UNIXTIME(created, '%Y%m') AS ym", "count(id) AS amount"))
                  ->group("ym")
                  ->order("ym DESC")
        );

        $entries = array();
        foreach ($resultSet as $row)
        {
            $year = substr($row->ym, 0, 4);
            $month = substr($row->ym, 4, 2);
            $entries[] = array(
                'year' => $year,
                'month' => $month,
                'time' => mktime(0, 0, 0, $month, 1, $year),
                'amount' => $row->amount
            );
        }
        return $entries;
    }

    public function getArchivedEvents($year, $month = null)
    {
        throw new Bigace_Events_Exception('Method getArchivedEvents() not implemented');
        $table = $this->getDbTable();
        if($month === null) {
            $start = mktime(0,0,0,1,1,$year);
            $end = strtotime("+1 year", $start);
        } else {
            $start = mktime(0,0,0,$month,1,$year);
            $end = strtotime("+1 month", $start);
        }
        $resultSet = $table->fetchAll(
            $table->select()
                  ->where("created > ?", $start)
                  ->where("created < ?", $end)
                  ->order("created DESC")
        );
        return $this->resultToArray($resultSet);
    }
    */

}
