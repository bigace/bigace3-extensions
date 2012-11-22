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
 * @version    $Id: Entry.php 4 2010-07-17 14:20:17Z kevin $
 */

/**
 * One comment entry.
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Comment_Entry
{
    const COMMENT = 'comment';
    const TRACKBACK = 'trackback';

    private $id;
    private $itemtype;
    private $itemid;
    private $language;
    private $name;
    private $email;
    private $homepage;
    private $ip;
    private $comment;
    private $timestamp;
    private $activated;
    private $anonymous;
    private $type;

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

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Bigace_Comment_Entry
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getItemtype()
    {
        return $this->itemtype;
    }

    /**
     * @param int $itemtype
     * @return Bigace_Comment_Entry
     */
    public function setItemtype($itemtype)
    {
        $this->itemtype = $itemtype;
        return $this;
    }

    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * @param int $itemid
     * @return Bigace_Comment_Entry
     */
    public function setItemid($itemid)
    {
        $this->itemid = $itemid;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return Bigace_Comment_Entry
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Bigace_Comment_Entry
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Bigace_Comment_Entry
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * @param string $homepage
     * @return Bigace_Comment_Entry
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return Bigace_Comment_Entry
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Bigace_Comment_Entry
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Bigace_Comment_Entry
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = intval($timestamp);
        return $this;
    }

    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * @param boolean $activated
     * @return Bigace_Comment_Entry
     */
    public function setActivated($activated)
    {
        $this->activated = (bool)$activated;
        return $this;
    }

    public function getAnonymous()
    {
        return (bool)$this->anonymous;
    }

    /**
     * @param boolean $anonymous
     * @return Bigace_Comment_Entry
     */
    public function setAnonymous($anonymous)
    {
        $this->anonymous = (bool)$anonymous;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Either Bigace_Comment_Entry::COMMENT or Bigace_Comment_Entry::TRACKBACK
     * @param string $type
     * @return Bigace_Comment_Entry
     */
    public function setType($type)
    {
        $type = strtolower($type);
        if ($type == self::COMMENT || $type == self::TRACKBACK)
            $this->type = $type;
        else
            throw new Bigace_Exception('Wrong comment type');
        return $this;
    }
}
