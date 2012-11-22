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
 * Class handling all Glossary entry database requests.
 *
 * @license http://www.bigace.de/license.html GNU Public License
 * @copyright Copyright (C) Kevin Papst (http://www.bigace.de)
 * @version $Id: Glossary.php 106 2011-03-15 10:16:38Z kevin $
 * @package Bigace_Zend
 */
class Bigace_Glossary extends Bigace_Db_Table_Abstract
{
    protected $_name = 'glossary';
    protected $_primary = array('cid', 'abbreviation');

    /**
     * Adds a glossary entry.
     *
     * @param  string $abbreviation the abbreviation
     * @param  string $description the description
     * @param  int    $id the id for this entry, will be created if not passed
     * @return mixed  the primary key of the row inserted
     */
    public function addEntry($abbreviation, $description, $id = null)
    {
        $data = array(
            'abbreviation'  => $abbreviation,
            'description'   => $description
        );

        if($id !== null)
            $data['id'] = $id;

        return $this->insert($data);
    }

    /**
     * Removes the glossary entry with the given abbreviation.
     *
     * @param string $abbreviation the abbreviation of the entry to remove
     */
    public function removeEntry($abbreviation)
    {
        $where = $this->getAdapter()->quoteInto('abbreviation = ?', $abbreviation);
        return $this->delete($where);
    }

    /**
     * Returns all available glossary entries.
     *
     * @param string $url the url to get the link entry for
     * @return array|null
     */
    public function getAllEntries()
    {
        $row = $this->fetchAll( $this->select() );

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * Removes all glossary entries.
     *
     * @param string $url the url to get the link entry for
     * @return array|null
     */
    public function removeAllEntries()
    {
        return $this->delete();
    }

    /**
     * Return a glossary entry.
     *
     * @param  string $abbreviation the abbreviation
     * @return null|array
     */
    public function getEntry($abbreviation)
    {
        $row = $this->fetchRow( $this->select()->where('abbreviation = ?', $abbreviation) );
        if($row === null)
            return null;

        return $row;
    }

    /**
     * Return an array with glossary entries.
     *
     * @param  array $ids the abbreviation
     * @return null|array
     */
    public function getAllById(array $ids)
    {
        if(count($ids) == 0)
            return array();

        $select = $this->select();
        $select->where("id IN (?)", $ids);
        $row = $this->fetchAll( $select );

        if($row === null)
            return null;

        return $row->toArray();
    }

}
