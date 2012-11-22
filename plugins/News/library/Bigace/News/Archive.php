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
 * @package    Bigace_News
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Archive.php 136 2011-03-17 11:52:45Z kevin $
 */

/**
 * Model class for handling virtual news archives.
 * This is not meant for direct instantiation, you will receive
 * it via Bigace_News_Service::getArchives()
 *
 * @category   Bigace
 * @package    Bigace_News
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_News_Archive
{
    private $ts;
    private $year;
    private $month;
    private $amount;

    /**
     * Instantiates a
     */
    function __construct($year, $month, $amount)
    {
        $this->year   = $year;
        $this->month  = $month;
        $this->ts     = mktime(0, 0, 0, $month, 1, $year);
        $this->amount = $amount;
    }

    /**
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->ts;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return 'news/archive/'.$this->year.'-'.$this->month.'/';
    }

    /**
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

}