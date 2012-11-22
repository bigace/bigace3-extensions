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
 * @version    $Id: Design.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * This represents a Design which is choseable in the Menu Administration.
 *
 * @category   Bigace
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Smarty_Design implements Bigace_View_Layout
{

    private $value;
    private $portlets;
    private $contents;

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
        // used in Bigace_Smarty_Design and Bigace_Smarty_Service
        $sqlString = 'SELECT * FROM {DB_PREFIX}design WHERE cid={CID} AND name={NAME}';
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $this->setArray($temp->next());
        unset($temp);
    }

    /**
     * @return String the Design Name
     */
    function getName()
    {
        return $this->value["name"];
    }

    /**
     * @return String the Design Description
     */
    function hasPortletSupport()
    {
        return ($this->value["portlets"] == '1');
    }

    /**
     * @return String the Design Description
     */
    function getDescription()
    {
        return $this->value["description"];
    }

    function getOptions()
    {
        return array('css' => 'editor.css');
    }

    /**
     * @see Bigace_View_Layout::getContentNames()
     */
    public function getBasePath()
    {
        return strtolower($this->getName());
    }

    /**
     * Returns the associated Stylesheet.
     *
     * @return Bigace_Smarty_Stylesheet
     */
    function getStylesheet()
    {
        return new Bigace_Smarty_Stylesheet($this->value["stylesheet"]);
    }

    /**
     * Returns the associated template.
     *
     * @return Bigace_Smarty_Template
     */
    function getTemplate()
    {
        return new Bigace_Smarty_Template($this->value["template"]);
    }

    /**
     * Returns an array with names of Portlet columns. Array might be empty.
     * @return array the supported Portlet columns
     */
    function getWidgetColumns()
    {
        if (is_null($this->portlets)) {
            $values = array('DESIGN' => $this->getName());

            $pt = array();
            $sqlString = 'SELECT * FROM {DB_PREFIX}design_portlets WHERE cid = {CID} AND design = {DESIGN}';
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

            foreach ($temp->getIterator() as $portlet) {
                $pt[] = $portlet['name'];
            }

            $this->portlets = $pt;
        }
        return $this->portlets;
    }

    /**
     * Returns an array of Strings, each String representing one Name of an additional content.
     * Each of these Content pieces will be editable as HTML Content.
     * If an empty array or null is returned, no additional content columns are defined.
     * @return array an array of Strings, an empty array or null
     */
    function getContentNames()
    {
        if (is_null($this->contents)) {
            $values = array('DESIGN' => $this->getName());

            $cnts = array();
            $sqlString = 'SELECT * FROM {DB_PREFIX}design_contents WHERE cid = {CID} AND design = {DESIGN}';
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

            foreach ($temp->getIterator() as $content) {
                $cnts[] = $content['name'];
            }

            $this->contents = $cnts;
        }
        return $this->contents;
    }
}
