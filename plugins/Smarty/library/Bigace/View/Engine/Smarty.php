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
 * @package    Bigace_View_Engine
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Smarty.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * Using the Smarty template engine to render pages.
 *
 * @category   Bigace
 * @package    Bigace_View_Engine
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_View_Engine_Smarty implements Bigace_View_Engine
{
    /**
     * @var Bigace_Smarty_Core
     */
    private $smarty = null;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->smarty = new Bigace_Smarty_Core();
    }

    /**
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * Returns the Controller name that is used to render templates/layouts.
     *
     * @return string the controller name in lower case
     */
    public function getControllerName()
    {
        return 'page';
    }

    /**
     * @see Bigace_View_Engine::getLayouts()
     * @return array
     */
    public function getLayouts()
    {
        $sm = new Bigace_Smarty_Service();
        return $sm->getAllDesigns();
    }

    /**
     * @see Bigace_View_Engine::getLayout()
     */
    public function getLayout($name = '')
    {
        if ($name == '') {
            $name = Bigace_Config::get('templates', 'default', 'default');
        }

        $sm = new Bigace_Smarty_Service();
        return $sm->getDesign($name);
    }

    /**
     * Throws exception when the given $layout does not exist.
     *
     * @see Bigace_View_Engine::startMvc()
     */
    public function startMvc($layout)
    {
        $design = new Bigace_Smarty_Design($layout);

        // another possible problem: assigned design doesn't exist
        if ($design->getName() == '') {
            throw new Bigace_Zend_Controller_Exception(
                array(
                    'message' => 'Could not find Smarty Template "'.$layout.'"',
                    'code'    => 500,
                    'script'  => 'community'
                ),
                array('backlink' => LinkHelper::url("/"))
            );
            return;
        }

        $layout = Zend_Layout::startMvc(
            array(
                'layout'     => 'smarty',
                'layoutPath' => BIGACE_APP_ROOT.'views/layouts/'
            )
        );

        $layout->assign('SMARTY_LAYOUT', $design);
        $layout->assign('SMARTY', $this->smarty);

        /*
        $tpl = $design->getTemplate();

        $layout = Zend_Layout::startMvc(
            array(
                'layout'     => $tpl->getFilename(),
                'layoutPath' => BIGACE_APP_ROOT.'views/layouts/',
                'view'       => new Bigace_Zend_View_Smarty($this->smarty)
            )
        );
        $layout->disableInflector();
        */
    }

    /**
     * Returns the SourceCode of the Layout.
     *
     * @param Bigace_View_Layout $layout
     * @return string
     */
    public function getSource(Bigace_View_Layout $layout)
    {
        $service = new Bigace_Smarty_Service();
        $design  = $service->getDesign($layout->getName());
        $tpl     = $design->getTemplate();

        return $tpl->getContent();
    }

    /**
     * Saves the SourceCode of the given $layout.
     *
     * @param Bigace_View_Layout $layout
     * @param string $content
     */
    public function save(Bigace_View_Layout $layout, $content)
    {
        $service = new Bigace_Smarty_Service();
        $design  = $service->getDesign($layout->getName());
        $tpl     = $design->getTemplate();

        if ($design === null) {
            throw new Bigace_Exception('Smarty design does not exist: ' . $layout->getName());
        }

        $service->updateTemplate(
            $tpl->getName(),
            $tpl->getDescription(),
            $content,
            $tpl->isInWork(),
            $tpl->isInclude()
        );
    }

    /**
     * Creates a new Layout with the given content as SourceCode.
     *
     * Can throw an Exception if a Layout with the name already exists.
     *
     * FIXME 3.0 implement me
     *
     * @param string $name
     * @param string $content
     * @throws Bigace_View_Exception
     * @return Bigace_View_Layout
     */
    public function create($name, $content = null)
    {
        // $service = new Bigace_Smarty_Service();

        // create template

        // get dummy stylesheet

        // create design

        throw new Bigace_Exception('Creating a new Layout through this API is not implemented yet.');
    }
}
