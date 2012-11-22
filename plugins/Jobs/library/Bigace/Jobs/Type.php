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
 * Class handling all Job types.
 *
 * @license http://www.bigace.de/license.html GNU Public License
 * @copyright Copyright (C) Kevin Papst (http://www.bigace.de)
 * @version $Id: Type.php 97 2011-03-15 10:10:18Z kevin $
 * @package Bigace_Zend
 */
class Bigace_Jobs_Type extends Bigace_Db_Table_Abstract
{
    protected $_name = 'jobs_type';
    protected $_primary = array('cid', 'id');

    /**
     * Adds a Job type.
     *
     * @param  string $name the name
     * @return mixed  the primary key of the row inserted
     */
    public function add($name)
    {
        $data = array(
            'title'  => $name
        );

        return $this->insert($data);
    }

    /**
     * Updates a Job type.
     *
     * @param  int    $id the jobtype id
     * @param  string $name the name
     * @return int    the number of updated rows
     */
    public function save($id, $name)
    {
        $data = array(
            'title'  => $name
        );

        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->update($data, $where);
    }

    /**
     * Removes the job type entry with the given id.
     *
     * @param  int    $id the jobtype id
     */
    public function remove($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    /**
     * Returns all available Job types.
     *
     * @return array|null
     */
    public function getAll()
    {
        $row = $this->fetchAll( $this->select() );

        if($row === null)
            return null;

        return $row->toArray();
    }


    /**
     * Returns the Job type with the given ID.
     *
     * @return array|null
     */
    public function get($id)
    {
        $select = $this->select()->where('id = ?', $id);
        $row = $this->fetchRow($select);

        if($row === null)
            return null;

        return $row->toArray();
    }

}
