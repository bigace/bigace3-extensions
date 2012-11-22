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
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: StylesheetsController.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * StylesheetController.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_StylesheetsController extends Bigace_Zend_Controller_Admin_Action
{
    // make sure smarty view can register their admin menus
    public function preInit()
    {
        $this->_viewEngine = Bigace_Services::get()->getService('view');
    }

    public function initAdmin()
    {
        if (!defined('STYLESHEET_CTRL')) {
			import('classes.util.formular.StylesheetSelect');

            $this->addTranslation('stylesheet');

			define('STYLESHEET_CTRL', true);
        }
    }

    public function indexAction()
    {
		$sid = '';
		if (isset($_GET['stylesheet'])) {
		    $sid = $_GET['stylesheet'];
		} else if (isset($_POST['stylesheet'])) {
		    $sid = urldecode($_POST['stylesheet']);
		}

		$service  = new Bigace_Smarty_Service();
		$selector = new StylesheetSelect();
		$selector->setName('editorcss');

		$entrys = $service->getAllStylesheets();
		$all    = array();

		foreach ($entrys AS $temp) {
			$temp = array(
				'name'          => $temp->getName(),
				'filename'      => urlencode($temp->getName()),
				'description'   => $temp->getDescription(),
				'usage'         => $service->countStylesheetUsage($temp->getName()),
				'edit'			=> $this->createLink('stylesheets', 'edit', array('name' => $temp->getName())),
				'delete'		=> $this->createLink('stylesheets', 'delete', array('name' => $temp->getName())),
			);
			$all[] = $temp;
		}

		$this->view->CREATE_URL = $this->createLink('stylesheets', 'create');
		$this->view->STYLESHEETS = $all;
		$this->view->EDITOR_CSS = $selector->getHtml();
    }

	// ---------------------------------- CREATE STYLESHEET ----------------------------------
	public function createAction()
	{
		$service = new Bigace_Smarty_Service();

		if (isset($_POST['stylesheet']) && trim($_POST['stylesheet']) != '' &&
		  isset($_POST['description']) && isset($_POST['editorcss'])) {
			$sid = $service->createStylesheet($_POST['stylesheet'], $_POST['description'], '', $_POST['editorcss']);
			if($sid === false)
				$this->view->ERROR = getTranslation('msg_saving_failed');
		} else {
			$this->view->ERROR = getTranslation('missing_values');
		}

		$this->_forward('index');
	}

	// ---------------------------------- EDIT STYLESHEET ----------------------------------
	public function editAction()
	{
		$sid = isset($_GET['name']) ? $_GET['name'] : (isset($_POST['name']) ? urldecode($_POST['name']) : '');

		$service = new Bigace_Smarty_Service();

		if ($sid != '') {
			$stylesheet = new Bigace_Smarty_Stylesheet($sid);

			if ($stylesheet->getName() != $sid) {
				$this->view->ERROR = "Stylesheet does not exist";
				$this->_forward('index');
				return;
			}

			$editorStylesheet = $stylesheet->getEditorStylesheet();;

			$selector = new StylesheetSelect();
			$selector->setPreselected($editorStylesheet->getName());
			$selector->setName('editorcss');

			$this->view->EDITOR_CHOOSER  = $selector->getHtml();
			$this->view->STYLESHEET_ID   = urlencode($stylesheet->getName());
			$this->view->STYLESHEET_NAME = $stylesheet->getName();
			$this->view->STYLESHEET_DESCRIPTION = $stylesheet->getDescription();
			$this->view->STYLESHEET_CONTENT = file_get_contents($stylesheet->getFullFilename());
			$this->view->SAVE_URL = $this->createLink('stylesheets', 'save');
			$this->view->BACK_URL = $this->createLink('stylesheets');
		} else {
			$this->view->ERROR = getTranslation('missing_values');
			$this->_forward('index');
			return;
		}
	}

	// ------------------------------------ SAVE STYLESHEET ------------------------------------
	public function saveAction()
	{
		$sid = isset($_POST['name']) ? urldecode($_POST['name']) : '';

		$service = new Bigace_Smarty_Service();

		if ($sid != '' && isset($_POST['description']) && isset($_POST['content']) && isset($_POST['editorcss'])) {
			$stylesheet = new Bigace_Smarty_Stylesheet($sid);

			if ($stylesheet->getName() != $sid) {
				$this->view->ERROR = "Stylesheet does not exist";
				$this->_forward('index');
				return;
			}

			$description = Bigace_Util_Sanitize::plaintext($_POST['description']);
            $editorCss   = Bigace_Util_Sanitize::plaintext($_POST['editorcss']);
			$content     = Bigace_Util_Sanitize::html($_POST['content']);

			if ($service->updateStylesheet($sid, $description, $content, $editorCss)) {
				$this->view->INFO = getTranslation('msg_saved');
			} else {
				$this->view->ERROR = getTranslation('msg_saving_failed');
			}

			$this->_forward('edit');
			return;
		} else {
			$this->view->ERROR = getTranslation('missing_values');
		}

		$this->_forward('index');
	}

	// ------------------------------------ DELETE STYLESHEET ------------------------------------
	public function deleteAction()
	{
		$sid = isset($_GET['name']) ? urldecode($_GET['name']) : '';

		$service = new Bigace_Smarty_Service();

		if ($sid != '') {
			$stylesheet = new Bigace_Smarty_Stylesheet($sid);

			if ($stylesheet->getName() != $sid) {
				$this->view->ERROR = "Stylesheet does not exist";
			} else {
				if ($service->countStylesheetUsage($sid) > 0) {
					$this->view->ERROR ='Cannot be deleted, Stylesheet in use.';
				} else {
					$service->deleteStylesheet($sid);
				}
			}
		} else {
			$this->view->ERROR = getTranslation('missing_values');
		}

		$this->_forward('index');
	}

}