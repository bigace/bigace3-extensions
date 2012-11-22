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
 * @package Bigace_Zend
 */

/**
 * Class handling all Jobs entries and database requests.
 *
 * @license http://www.bigace.de/license.html GNU Public License
 * @copyright Copyright (C) Kevin Papst (http://www.bigace.de)
 * @version $Id: Jobs.php 97 2011-03-15 10:10:18Z kevin $
 * @package Bigace_Zend
 */
class Bigace_Jobs extends Bigace_Db_Table_Abstract
{
    protected $_name = 'jobs';
    protected $_primary = array('cid', 'id');

    /**
     * Adds a Job entry.
     *
     * @return mixed  the primary key of the row inserted
     */
    public function add($title, $typeId, $description, $validTo, $jobDocId, $personDocId, $addDocId)
    {
        $data = array(
            'title'         => $title,
            'job_type'      => $typeId,
            'description'   => $description,
            'valid_to'      => $validTo,
            'job_doc'       => $jobDocId,
            'person_doc'    => $personDocId,
            'additional_doc'=> $addDocId
        );

        return $this->insert($data);
    }

    /**
     * Updates an existing Job entry.
     *
     * @return int  the number of updated rows
     */
    public function save($id, $title, $typeId, $description, $validTo, $jobDocId, $personDocId, $addDocId)
    {
        $data = array(
            'title'         => $title,
            'job_type'      => $typeId,
            'description'   => $description,
            'valid_to'      => $validTo,
            'job_doc'       => $jobDocId,
            'person_doc'    => $personDocId,
            'additional_doc'=> $addDocId
        );

        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->update($data, $where);
    }

    /**
     * Removes the Job entry with the given ID.
     *
     * @param string $abbreviation the abbreviation of the entry to remove
     */
    public function remove($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    /**
     * Returns all available Job entries.
     *
     * @return array|null
     */
    public function getAll()
    {
        $row = $this->fetchAll($this->select());

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * Returns all available Job entries that are still open.
     *
     * @return array|null
     */
    public function getAllOpen()
    {
        $where = $this->select();
        $where->where('valid_to >= ?', time());

        $row = $this->fetchAll($where);

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * Returns all available Job entries that are still open and
     * of the given type.
     *
     * @return array|null
     */
    public function getAllOpenForType($type)
    {
        $where = $this->select();
        $where->where('valid_to >= ?', time())
              ->where('job_type = ?', $type);

        $row = $this->fetchAll($where);

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * Returns one Job by its ID.
     *
     * @return array|null
     */
    public function get($id)
    {
        $where = $this->select();
        $where->where('id = ?', $id);

        $row = $this->fetchRow($where);

        if($row === null)
            return null;

        return $row->toArray();
    }
    /**
     * Returns all available Job entries that are expired.
     *
     * @return array|null
     */
    public function getAllClosed()
    {
        $where = $this->select();
        $where->where('valid_to <= ?', time());

        $row = $this->fetchAll($where);

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * Returns the latest open/available jobs.
     * @return array
     */
    public function getLatest($count = null, $offset = null)
    {
        if($count === null) $count = 3;

        $s = $this->select();
        $s->where('valid_to >= ?', time())
          ->limit($count, $offset);

        $row = $this->fetchAll($s);

        if($row === null)
            return null;

        return $row->toArray();
    }


    /**
     * Returns all entries for one Jobtype.
     *
     * @param  int    $id the jobtype id
     * @return array|null
     */
    public function getByType($typeId)
    {
        $where = $this->select();
        $where->where('job_type = ?', $typeId);

        $row = $this->fetchAll($where);

        if($row === null)
            return null;

        return $row->toArray();
    }

}
