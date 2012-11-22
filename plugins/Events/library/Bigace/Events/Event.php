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
 * @version    $Id: Event.php 108 2011-03-15 10:20:55Z kevin $
 */

/**
 * An event.
 *
 * @category   Bigace
 * @package    Bigace_Events
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Events_Event
{
    protected $id;
    protected $title;
    protected $type;
    protected $description;
    protected $details;
    protected $location;
    protected $artist;
    protected $startdate;
    protected $enddate;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = (string) $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDetails($details)
    {
        $this->details = (string) $details;
        return $this;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setLocation($location)
    {
        $this->location = (string) $location;
        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setArtist($artist)
    {
        $this->artist = (string) $artist;
        return $this;
    }

    public function getArtist()
    {
        return $this->artist;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setStartDate($date)
    {
        $this->startdate = (int) $date;
        return $this;
    }

    public function getStartDate()
    {
        return $this->startdate;
    }

    public function setEndDate($date)
    {
        $this->enddate = (int) $date;
        return $this;
    }

    public function getEndDate()
    {
        return $this->enddate;
    }

}

