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
 * @package    Bigace_Zend
 * @subpackage View
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Smarty.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * Smarty view for Zend Framework.
 *
 * Customized to use the Smarty version shipped with Bigace.
 * Will be pre-configured for Community usage.
 *
 * TODO translate phpdoc
 *
 * @category   Bigace
 * @package    Bigace_Zend
 * @subpackage View
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Zend_View_Smarty implements Zend_View_Interface
{
    /**
     * Smarty object
     *
     * @var Bigace_Smarty_Core
     */
    protected $smarty;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct(Smarty $smarty, $extraParams = array())
    {
        $this->smarty = $smarty;

        //$this->smarty->assignByRef('this', $this);

        foreach ($extraParams as $key => $value) {
            $this->smarty->$key = $value;
        }
    }

    /**
     * Gebe das aktuelle Template Engine Objekt zurück
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->smarty;
    }

    /**
     * Setze den Pfad zu den Templates
     *
     * @param string $path Das Verzeichnis, das als Pfad gesetzt werden soll.
     * @return void
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
           // $this->smarty->setTemplateDir($path);
            return;
        }

        throw new Bigace_Exception('Invalid path provided');
    }

    /**
     * Empfange das aktuelle template Verzeichnis
     *
     * @return string
     */
    public function getScriptPaths()
    {
        return array($this->smarty->template_dir);
    }

    /**
     * Alias für setScriptPath
     *
     * @param string $path
     * @param string $prefix nicht verwendet
     * @return void
     */
    public function setBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * Alias für setScriptPath
     *
     * @param string $path
     * @param string $prefix nicht verwendet
     * @return void
     */
    public function addBasePath($path, $prefix = 'Zend_View')
    {
        if (is_readable($path)) {
          //  $this->smarty->addTemplateDir($path);
            return;
        }

        throw new Bigace_Exception('Invalid path provided');
    }

    /**
     * Weise dem Template eine Variable zu
     *
     * @param string $key der Variablenname.
     * @param mixed $val der Variablenwert.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->smarty->assign($key, $val);
    }

    /**
     * Erlaubt das Testen von empty() und isset()
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->smarty->getTemplateVars($key));
    }

    /**
     * Erlaubt das Zurücksetzen von Objekteigenschaften
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->smarty->clearAssign($key);
    }

    /**
     * Assigns a template variable.
     *
     * Erlaubt das Zuweisen eines bestimmten Wertes zu einem bestimmten
     * Schlüssel, ODER die Übergabe eines Array mit Schlüssel => Wert
     * Paaren zum Setzen in einem Rutsch.
     *
     * @see __set()
     * @param string|array $spec Die zu verwendene Zuweisungsstrategie
     * (Schlüssel oder Array mit Schlüssel => Wert paaren)
     * @param mixed $value (Optional) Wenn ein Variablenname verwendet wurde,
     *                                verwende diesen als Wert
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->smarty->assign($spec);
            return;
        }

        $this->smarty->assign($spec, $value);
    }

    /**
     * Setze alle zugewiesenen Variablen zurück.
     *
     * Clears all variables, which were set with {@link assign()} or assigning them
     * through the magic methods ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->smarty->clearAllAssign();
    }

    /**
     * Returns the response from the template $name.
     *
     * @param string $name the template to render
     * @return string
     */
    public function render($name)
    {
        $this->prepareBeforeRender();
        return $this->smarty->fetch($name);
    }

    protected function prepareBeforeRender($name)
    {
        /*
        // fix for some old smarty tags that rely on this variable
        $GLOBALS['MENU'] = $this->MENU;
        $this->smarty->assign('MENU', $this->MENU);
        $this->smarty->assign('USER', $this->USER);

        $cntNames = $this->layout()->CONTENT_NAMES;

        foreach ($cntNames as $name) {
            $this->smarty->assign($name, $this->layout()->$name);
        }
        */

        $this->smarty->assign('VIEW', $this); // new since 3.0

        /*
        $this->smarty->assign('LAYOUT', $this->LAYOUT); // new since 3.0
        $this->smarty->assign('CONTENT', $this->layout()->content);
        */
    }

}