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
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Template.php 164 2011-03-25 09:39:42Z kevin $
 */

/**
 * A Smarty-Template combined with a Smarty-Stylesheet is a Smarty-Design, which
 * is chooseable in the Menu Administration.
 *
 * @category   Bigace
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Smarty_Template
{
    private $value;
    private $baseFolder = null;

    public function __construct($name = null)
    {
    	if ($name != null) {
    		$this->loadByName($name);
    	}
    }

    /**
     * @access protected
     */
    function setArray($values)
    {
    	$this->value = $values;
    }

    /**
     * Loads a template by its name.
     */
    private function loadByName($name)
    {
	    $values = array( 'NAME' => $name );
        $sqlString = 'SELECT * FROM {DB_PREFIX}template WHERE cid={CID} AND name={NAME}';
	    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->setArray($temp->next());
        unset($temp);
    }

    function getName()
    {
        return $this->value["name"];
    }

    function getDescription()
    {
        return $this->value["description"];
    }

    function getFilename()
    {
        return $this->value["filename"];
    }

    private function getSmartyFolder()
    {
        if ($this->baseFolder === null) {
            if (!Zend_Registry::isRegistered('BIGACE_COMMUNITY')) {
                throw new Bigace_View_Exception('Community was null, cannot detect smarty folder.');
            }
            $community        = Zend_Registry::get('BIGACE_COMMUNITY');
            $this->baseFolder = $community->getPath() . 'views/smarty/';
        }
        return $this->baseFolder;
    }

	function getFullURL()
	{
		return $this->getSmartyFolder() . $this->getFilename();
	}

    function isInWork()
    {
        return ($this->value["inwork"] == 1 ? true : false);
    }

	/**
	 * If is a System Template that might NOT be deleted!
	 */
    function isSystem()
    {
        return ($this->value["system"] == 1 ? true : false);
    }

    function isInclude()
    {
        return ($this->value["include"] == 1 ? true : false);
    }

    function getTimestamp()
    {
        return $this->value["timestamp"];
    }

    function getChangedBy()
    {
        return $this->value["userid"];
    }

    function getContent()
    {
		// also used in Bigace_Smarty_Core, change both!
        return file_get_contents($this->getFullURL());
    }

}