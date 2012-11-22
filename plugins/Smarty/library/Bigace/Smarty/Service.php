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
 * @version    $Id: Service.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * The Service is capable for writing and reading Smarty Objects.
 *
 * @category   Bigace
 * @package    Bigace_Smarty
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Smarty_Service
{

    /**
     * Saves the Content to the given File, by replacing the old File content.
     *
     * @param string $filename
     * @param mixed $content
     * @throws Bigace_Exception
     * @return boolean
     */
    private function saveContent($filename, $content)
    {
        try {
			// FIXME 3.0 use IOHelper instead ????
            $fpointer = fopen($filename, "wb");
            fputs($fpointer, $content);
            //$GLOBALS['LOGGER']->logInfo("Writing Content to File '" . $filename . "'");
            fclose($fpointer);
            return true;
        } catch(Exception $ex) {
            throw new Bigace_Exception('Could not write file: ' . $filename);
        }
        return false;
    }

    private function checkFile($filename)
    {
        return is_writable($filename);
    }

    /**
     * Creates a valid (and useful) Filename from any String.
     * It cuts off all Character except:
     * a - z
     * A - Z
     * 0 - 9
     * and the two separator character '_' and '-'.
     *
     * @param string $name
     * @return string
     */
    public function parseName($name)
    {
        return trim(str_replace(" ", "", preg_replace("/[^a-zA-Z0-9_-\\s]/", "", $name)));
    }

    /**
     * Deletes a histiry entry.
     */
    private function deleteHistory($type, $name)
    {
        $values = array('NAME' => $name,
                        'TYPE' => $type);
        $sql = "DELETE FROM {DB_PREFIX}smarty_history WHERE type={TYPE} AND name={NAME} AND cid={CID}";
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
    }

    /**
     * Creates a history entry.
     */
    private function createHistory($type, $name, $content)
    {
        $values = array(
            'name'      => $name,
            'content'   => $content,
            'timestamp' => time(),
            'userid'    => $GLOBALS['_BIGACE']['SESSION']->getUserID(),
            'type'      => $type
      );
        return $GLOBALS['_BIGACE']['SQL_HELPER']->insert("smarty_history", $values);
    }

    /**
     * Counts how often a Template is used in Designs.
     * @return int the amount of how often the Template is in use
     */
    public function countTemplateUsage($name)
    {
        $values = array('TEMPLATE' => $name);
        $sql = 'SELECT count(name) as amount FROM {DB_PREFIX}design WHERE template={TEMPLATE} AND cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        $tpl = $tpl->next();
        return $tpl['amount'];
    }

    /**
     * Counts how often a Stylesheet is used in Designs.
     *
     * @return int the amount of how often the Stylesheet is in use
     */
    public function countStylesheetUsage($name)
    {
        $values = array('STYLESHEET' => $name);
        $sql = 'SELECT count(name) as amount FROM {DB_PREFIX}design WHERE stylesheet={STYLESHEET} AND cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        $tpl = $tpl->next();
        return $tpl['amount'];
    }

    /**
     * Counts how often a Design is used by Menus.
     *
     * @return int the amount of how often the Design is in use
     */
    public function countDesignUsage($name)
    {
        $values = array('DESIGN' => $name);
        $sql = 'SELECT count(id) as amount FROM {DB_PREFIX}item_1 WHERE text_4={DESIGN} AND cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        $tpl = $tpl->next();
        return $tpl['amount'];
    }

    /**
     * Creates a new Design.
     *
     * @return boolean
     */
    public function createDesign($name,$description,$template,$stylesheet,$portlets)
    {
        $values = array(
            'template'    => $template,
            'stylesheet'  => $stylesheet,
            'name'        => $name,
            'description' => $description,
            'portlets'    => ($portlets == true ? 1 : 0)
        );
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->insert("design", $values);
        return $res !== false;
    }

    /**
     * Creates a new Template.
     * The Filename is built by calling <code>$this->parseName($name)</code>.
     *
     * @return boolean
     */
    public function createTemplate($name,$description,$content,$include = false,$inwork= true)
    {
        $filename = urlencode($this->parseName($name) . '.tpl');

        if (!Zend_Registry::isRegistered('BIGACE_COMMUNITY')) {
            throw new Bigace_View_Exception('Community was null, cannot detect smarty folder.');
        }
        $community = Zend_Registry::get('BIGACE_COMMUNITY');
        $fullUrl   = $community->getPath() . 'views/smarty/' . $filename;

        if ($this->saveContent($fullUrl, $content)) {
            $values = array('inwork'        => ((is_bool($inwork) && $inwork) ? '1' : '0'),
                            'include'       => ((is_bool($include) && $include) ? '1' : '0'),
                            'name'          => $name,
                            'description'   => $description,
                            'filename'      => $filename,
                            'timestamp'     => time(),
                            'userid'        => $GLOBALS['_BIGACE']['SESSION']->getUserID()
            );
            $res = $GLOBALS['_BIGACE']['SQL_HELPER']->insert("template", $values);
            return $res !== false;
        }
    }

    /**
     * Creates a new Stylesheet.
     * The Filename is built by calling <code>$this->parseName($name)</code>.
     */
    public function createStylesheet($name,$description,$content,$editorcss)
    {
        $filename = $this->parseName($name);
        $filename = urlencode($filename.'.css');
        $fullUrl = BIGACE_DIR_PUBLIC_CID . $filename;

        $values = array('name'        => $name,
                        'description' => $description,
                        'filename'    => $filename,
                        'editorcss'   => $editorcss);

        if ($this->checkFile($fullUrl)) {
            if ($this->saveContent($fullUrl, $content)) {
                return $GLOBALS['_BIGACE']['SQL_HELPER']->insert("stylesheet", $values);
            }
        }
        $GLOBALS['LOGGER']->logError('Check file permissions. '.$fullUrl.' is not writeable!');
        return false;
    }

    /**
     * Name is not updateable yet!
     * @return boolean
     */
    public function updateTemplate($name,$description,$content,$inwork,$include)
    {
        $template = new Bigace_Smarty_Template($name);

        $fullUrl = $template->getFullURL();

        if ($this->checkFile($fullUrl)) {
            if ($this->saveContent($fullUrl, $content)) {
                $this->createHistory('template', $name, $template->getContent());

                $values = array('INWORK'        => ((is_bool($inwork) && $inwork) ? '1' : '0'),
                                'INCLUDE'       => ((is_bool($include) && $include) ? '1' : '0'),
                                'NAME'          => $name,
                                'DESCRIPTION'   => $description,
                                'TIMESTAMP'     => time(),
                                'USERID'        => $GLOBALS['_BIGACE']['SESSION']->getUserID()
                            );
                $sql = 'UPDATE {DB_PREFIX}template SET description={DESCRIPTION},'.
                    'inwork={INWORK},include={INCLUDE},timestamp={TIMESTAMP},userid={USERID} '.
                    'WHERE cid={CID} AND NAME={NAME}';
                $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
                $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
                return $res !== false;
            }
        }

        $GLOBALS['LOGGER']->logError('Check file permissions. '.$fullUrl.' is not writeable!');
        return false;
    }

    /**
     * Name is not updateable yet!
     * @return boolean
     */
    public function updateStylesheet($name,$description,$content,$editorcss)
    {
        $design = new Bigace_Smarty_Stylesheet($name);
        $fullUrl = $design->getFullFilename();

        $this->createHistory('stylesheet', $name, file_get_contents($fullUrl));

        if (!$this->checkFile($fullUrl)) {
            $GLOBALS['LOGGER']->logError('Check file permissions: '.$fullUrl.' is not writeable');
            return false;
        }

        if ($this->saveContent($fullUrl, $content)) {
            $values = array('NAME'          => $name,
                            'DESCRIPTION'   => $description,
                            'EDITORCSS'     => $editorcss);
            $sql = 'UPDATE {DB_PREFIX}stylesheet SET description={DESCRIPTION}, ' .
                   'editorcss={EDITORCSS} WHERE cid={CID} AND NAME={NAME}';
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
            $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
            return $res !== false;
        }
    }

    /**
     * Name is not updateable!
     * @return boolean
     */
    public function updateDesign($name,$description,$template,$stylsheet,$portlets)
    {
        $values = array('STYLESHEET'    => $stylsheet,
                        'TEMPLATE'      => $template,
                        'NAME'          => $name,
                        'DESCRIPTION'   => $description,
                        'PORTLETS'      => ($portlets == true ? 1 : 0)
        );

        $sql = 'UPDATE {DB_PREFIX}design SET description={DESCRIPTION},stylesheet={STYLESHEET},'.
               'template={TEMPLATE},portlets={PORTLETS} WHERE cid={CID} AND NAME={NAME}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        return $res !== false;
    }

    /**
     * Delete the given Template.
     */
    public function deleteTemplate($name)
    {
        if ($this->countTemplateUsage($name) == 0) {
            $template = new Bigace_Smarty_Template($name);
            if (unlink($template->getFullURL())) {
                $this->deleteHistory('template', $template->getName());
                $sql = 'DELETE FROM {DB_PREFIX}template WHERE name={NAME} AND cid={CID}';
                $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array('NAME' => $name), true);
                return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
            }
        } else {
            $GLOBALS['LOGGER']->logError('Template "'.$name.'" cannot be deleted, in use!');
        }
        return false;
    }

    /**
     * Delete the given Stylesheet.
     */
    public function deleteStylesheet($name)
    {
        if ($this->countStylesheetUsage($name) == 0) {
            $style = new Bigace_Smarty_Stylesheet($name);
            if (unlink($style->getFullFilename())) {
                $this->deleteHistory('stylesheet', $style->getName());
                $values = array('NAME' => $name);
                $sql = 'DELETE FROM {DB_PREFIX}stylesheet WHERE name={NAME} AND cid={CID}';
                $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
                return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
            } else {
                $GLOBALS['LOGGER']->logError('Check file permissions: '.$style->getFullFilename().' could not be deleted!');
            }
        } else {
            $GLOBALS['LOGGER']->logError('Stylesheet "'.$name.'" cannot be deleted, in use!');
        }
        return false;
    }

    /**
     * Delete the given Design.
     */
    public function deleteDesign($name)
    {
        if ($this->countDesignUsage($name) == 0) {
            $template = new Bigace_Smarty_Design($name);
            $values = array('NAME' => $name);
            $sql = 'DELETE FROM {DB_PREFIX}design WHERE name={NAME} AND cid={CID}';
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
            return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        } else {
            $GLOBALS['LOGGER']->logError('Design "'.$name.'" cannot be deleted, in use!');
        }
        return false;
    }

    public function getAllTemplates($showIncludes = true)
    {
        $all = array();
        $sql = 'SELECT * FROM {DB_PREFIX}template WHERE cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array());
        $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        for ($i=0; $i < $entrys->count(); $i++) {
            $temp = new Bigace_Smarty_Template();
            $temp->setArray($entrys->next());
            if(!$temp->isInclude() || $showIncludes)
                $all[] = $temp;
        }
        return $all;
    }

    /**
     * Fetches all Designs as array.
     *
     * @return array(Bigace_Smarty_Design)
     */
    public function getAllDesigns()
    {
        $all = array();
        $sql = 'SELECT * FROM {DB_PREFIX}design WHERE cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array());
        $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        for ($i=0; $i < $entrys->count(); $i++) {
            $temp = new Bigace_Smarty_Design();
            $temp->setArray($entrys->next());
            $all[] = $temp;
        }
        return $all;
    }

    /**
     * Fetches all Stylesheets as array.
     *
     * @return array(Bigace_Smarty_Stylesheet)
     */
    public function getAllStylesheets()
    {
        $all = array();
        $sql = 'SELECT * FROM {DB_PREFIX}stylesheet WHERE cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array());
        $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        for ($i=0; $i < $entrys->count(); $i++) {
            $temp = new Bigace_Smarty_Stylesheet();
            $temp->setArray($entrys->next());
            $all[] = $temp;
        }
        return $all;
    }

    /**
     * Sets which portlet columns are availbale for the given design
     *
     * @param string $design
     * @param array $columnNameArray
     */
    public function setPortlets($design, $columnNameArray)
    {
        $values = array('DESIGN' => $design);
        $sql = 'DELETE FROM {DB_PREFIX}design_portlets WHERE design={DESIGN} AND cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        foreach ($columnNameArray as $name) {
            $values = array('DESIGN' => $design, 'NAME' => $name);
            $sql = 'INSERT INTO {DB_PREFIX}design_portlets (cid,design,name) VALUES ({CID},{DESIGN},{NAME})';
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
            $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        }
    }

    /**
     * Sets which contents are available for a design.
     *
     * @param string $design
     * @param array $columnNameArray
     */
    public function setContents($design, $columnNameArray)
    {
        $values = array('DESIGN' => $design);
        $sql = 'DELETE FROM {DB_PREFIX}design_contents WHERE design={DESIGN} AND cid={CID}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);

        foreach ($columnNameArray AS $name) {
            $values = array('DESIGN' => $design, 'NAME' => $name);
            $sql = 'INSERT INTO {DB_PREFIX}design_contents (cid,design,name) VALUES ({CID},{DESIGN},{NAME})';
            $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
            $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        }
    }

    /**
     * Returns the Design or null.
     *
     * @return Bigace_Smarty_Design|null
     */
    public function getDesign($name)
    {
        $values = array( 'NAME' => $name );
        // used in Bigace_Smarty_Design and Bigace_Smarty_Service
        $sql = 'SELECT * FROM {DB_PREFIX}design WHERE cid={CID} AND name={NAME}';
        $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $temp = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sql);
        if($temp->count() > 0)
            return new Bigace_Smarty_Design($name);

        return null;
    }

}