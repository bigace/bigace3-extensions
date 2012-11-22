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
 * @version    $Id: TemplatesController.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * TemplatesController.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_TemplatesController extends Bigace_Zend_Controller_Admin_Action
{
    // make sure smarty view can register their admin menus
    public function preInit()
    {
        $this->_viewEngine = Bigace_Services::get()->getService('view');
    }

    public function initAdmin()
    {
        if (!defined('TEMPLATES_CTRL')) {
			import('classes.util.formular.TemplateSelect');

            $this->addTranslation('templates');

			define('TEMPLATES_CTRL', true);
        }
    }

    public function indexAction()
    {
		$service = new Bigace_Smarty_Service();

		$entrys = $service->getAllTemplates(true);

		$all = array();
		$system = array();
		foreach ($entrys AS $tpl) {
			$temp = array(
				'name'          => $tpl->getName(),
				'filename'      => $tpl->getFilename(),
				'description'   => $tpl->getDescription(),
				'inWork'        => $tpl->isInWork(),
				'include'       => $tpl->isInclude(),
				'system'        => $tpl->isSystem(),
				'usage'         => $service->countTemplateUsage($tpl->getName()),
				'edit'			=> $this->createLink(
				    'templates', 'edit', array('template' => urlencode($tpl->getName()))
			    ),
				'delete'		=> $this->createLink(
				    'templates', 'delete', array('template' => urlencode($tpl->getName()))
			    )
			);
			if($tpl->isSystem())
				$system[] = $temp;
			else
				$all[] = $temp;
		}

		$selector = new TemplateSelect();
		$selector->setShowIncludes(true);
		$selector->setShowDeactivated(true);
		$selector->setShowSystemTemplates(true);
		$selector->setName('copyname');


		$this->view->CREATE_URL = $this->createLink('templates', 'create');
		$this->view->system = $system;
		$this->view->templates = $all;
		$this->view->tplcopy = $selector->getHtml();
    }

	public function deleteAction()
	{
	    $tid = $this->getRequest()->getParam('template', '');
		$tis = urldecode($tid);

		$template = ($tid != '') ? new Bigace_Smarty_Template($tid) : null;

		if ($template != null && $tid != '' && $template->getName() == $tid) {
			$service = new Bigace_Smarty_Service();

			if ($template->isSystem()) {
				$this->view->ERROR = 'System templates cannot be deleted.';
			} else if ($service->countTemplateUsage($template->getName()) > 0) {
				$this->view->ERROR = 'Cannot be deleted, template in use.';
			} else {
				$service->deleteTemplate($template->getName());
			}
		} else {
			$this->view->ERROR = getTranslation('missing_values');
		}

		$this->_forward('index');
	}

	public function createAction()
	{
        $tid = $this->getRequest()->getParam('template', '');

		if (isset($_POST['templatename']) && trim($_POST['templatename']) != ''
			&& ( (isset($_POST['description']) && isset($_POST['isinclude'])) ||
				 (isset($_POST['mode']) && isset($_POST['copyname']) && $_POST['copyname'] != '') )) {
			$service = new Bigace_Smarty_Service();

			$newCnt = '';
			$inwork = true;
			$newName = $_POST['templatename'];
			$newDesc = isset($_POST['description']) ? $_POST['description'] : '';
			$include = (isset($_POST['isinclude']) && $_POST['isinclude'] == '1') ? true : false;

            $newName = Bigace_Util_Sanitize::plaintext($newName);
            $newDesc = Bigace_Util_Sanitize::plaintext($newDesc);
            $copyName = urldecode($_POST['copyname']);

			if (isset($_POST['mode']) && $_POST['mode'] == 'copy') {
				$copyTemplate = new Bigace_Smarty_Template($copyName);

				if ($copyTemplate->getName() == $copyName) {
					if(strlen($newDesc) == 0)
						$newDesc = $copyTemplate->getDescription();

					$include = $copyTemplate->isInclude();
					$inwork = $copyTemplate->isInWork();
					$newCnt = $copyTemplate->getContent();
				}
			}

			$res = $service->createTemplate($newName, $newDesc, $newCnt, $include, $inwork);
			if($res === false)
			  $this->view->ERROR = 'Could not create Template';
		} else {
			$this->view->ERROR = getTranslation('missing_values');
		}

		$this->_forward('index');
	}

	public function editAction()
	{
	    $tid = $this->getRequest()->getParam('template', '');
		$tis = urldecode($tid);

		$template = ($tid != '') ? new Bigace_Smarty_Template($tid) : null;

		if ($template === null || $tid == '' || $template->getName() != $tid) {
		    $this->view->ERROR = getTranslation('missing_values');
		    return;
		}

		$this->view->NAME =  $template->getName();
		$this->view->TEMPLATE_NAME =  urlencode($template->getName());
		$this->view->TEMPLATE_DESCRIPTION =  $template->getDescription();

		if ($template->isInWork()) {
			$this->view->INWORK_FALSE_CHECKED =  '';
			$this->view->INWORK_TRUE_CHECKED =  'checked="checked"';
		} else {
			$this->view->INWORK_FALSE_CHECKED =  'checked="checked"';
			$this->view->INWORK_TRUE_CHECKED =  '';
		}

		if ($template->isInclude()) {
			$this->view->IS_INCLUDE_CHECKED =  'checked="checked"';
		} else {
			$this->view->IS_INCLUDE_CHECKED =  '';
		}

		$this->view->FILENAME         = $template->getFilename();
		$this->view->USER             = $template->getChangedBy();
		$this->view->LAST_EDITED      = $template->getTimestamp();
		$this->view->TEMPLATE_CONTENT = $template->getContent();
		$this->view->SAVE_URL         = $this->createLink('templates', 'save');
		$this->view->BACK_URL         = $this->createLink('templates');
	}

    /**
     * Updates an existing template.
     *
     * Expires the page cache, because its content will likely change rendered pages content.
     */
	public function saveAction()
	{
		if (isset($_POST['template']) && isset($_POST['description']) &&
		    isset($_POST['tplcontent']) && isset($_POST['inwork'])) {

		    $service = new Bigace_Smarty_Service();
            $newName = Bigace_Util_Sanitize::plaintext(urldecode($_POST['template']));
            $newDesc = Bigace_Util_Sanitize::plaintext($_POST['description']);
			$include = (isset($_POST['isinclude']) ? true : false);
			$inwork  = (isset($_POST['inwork']) &&  $_POST['inwork'] == '1' ? true : false);
			$content = Bigace_Util_Sanitize::html($_POST['tplcontent']);

			if ($service->updateTemplate($newName, $newDesc, $content, $inwork, $include)) {
				$this->view->INFO = getTranslation('msg_saved');

                // expire page cache, a changed template will likely change page rendering somehow
                Bigace_Hooks::do_action('expire_page_cache');
			} else {
				$this->view->ERROR = getTranslation('msg_saving_failed');
			}
			$this->_forward('edit');
			return;
        }

		$this->view->ERROR = getTranslation('missing_values');
		$this->_forward('index');
	}

}