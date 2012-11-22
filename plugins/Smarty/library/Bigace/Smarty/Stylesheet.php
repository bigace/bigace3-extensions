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
 * @version    $Id: Stylesheet.php 164 2011-03-25 09:39:42Z kevin $
 */

/**
 * This represents a Stylesheet to be used in a Design.
 *
 * @category   Bigace
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Smarty_Stylesheet
{
    private $value;

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
     * @access private
     */
    function loadByName($name)
    {
	    $values = array( 'NAME' => $name );
        $sql = 'SELECT * FROM {DB_PREFIX}stylesheet WHERE cid={CID} AND name={NAME}';
	    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
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

    /**
     * Returns the Stylesheet that is used for the Editor.
     *
     * @return Bigace_Smarty_Stylesheet
     */
    function getEditorStylesheet()
    {
    	if($this->value["editorcss"] == null || $this->value["editorcss"] == '')
    		return $this;
        return new Bigace_Smarty_Stylesheet($this->value["editorcss"]);
    }

	/**
	 * The absolute Filename on the hard drive.
	 * DO NOT USE AS src ATTRIBUTE IN HTML!
	 */
	function getFullFilename()
	{
        return BIGACE_DIR_PUBLIC_CID . $this->value["filename"];
    }

	/**
	 * Returns the URL for the Stylesheet, to be used in HTML.
	 * @return String the URL to the Stylesheet
	 */
	function getURL()
	{
        return BIGACE_URL_PUBLIC_CID . $this->value["filename"];
    }
}