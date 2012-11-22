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
 * @version    $Id: DesignController.php 190 2011-04-04 14:40:02Z kevin $
 */

/**
 * DesignController.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_DesignController extends Bigace_Zend_Controller_Admin_Action
{
    // make sure smarty view can register their admin menus
    public function preInit()
    {
        $this->_viewEngine = Bigace_Services::get()->getService('view');
    }

    public function initAdmin()
    {
        if (!defined('DESIGN_CTRL')) {
            import('classes.util.formular.TemplateSelect');
            import('classes.util.formular.StylesheetSelect');

            define('PARAM_DESIGN_NAME', 'designName');
            define('PARAM_STYLE_MODE', 'mode'); // FIXME remove

            $this->addTranslation('design');

            define('DESIGN_CTRL', true);
        }
    }

    // LIST ALL DESIGN
    public function indexAction()
    {
        $service = new Bigace_Smarty_Service();

        $entrys = $service->getAllDesigns();

        $designs = array();
        foreach ($entrys AS $temp) {
            // FIXME use URLs instead of disabled select
            // then allow direct editing of stylesheets and templates
            $template = $temp->getTemplate();
            $stylesheet = $temp->getStylesheet();

            $selector = new TemplateSelect();
            $selector->setPreselected($template->getName());
            $selector->setShowIncludes(false);
            $selector->setShowDeactivated(false);
            $selector->setTagAttribute('disabled', 'disabled');
            $selector->setShowPreselectedIfDeactivated(true);
            $selector->setName('template');

            $selector2 = new StylesheetSelect();
            $selector2->setPreselected($stylesheet->getName());
            $selector2->setName('stylesheet');
            $selector2->setTagAttribute('disabled', 'disabled');

            $designs[] = array(
                'DESIGN' => $temp,
                'EDIT_URL' => $this->createLink('design', 'edit', array(PARAM_DESIGN_NAME => urlencode($temp->getName()))),
                'SAVE_URL' => $this->createLink('design', 'update'),
                'DELETE_URL' => $this->createLink('design', 'delete', array(PARAM_DESIGN_NAME => urlencode($temp->getName()))),
                'TEMPLATE' => $selector->getHtml(),
                'STYLESHEET' => $selector2->getHtml(),
                'ALLOW_DELETE' => ($service->countDesignUsage($temp->getName()) == 0)
            );
        }

        $selector = new TemplateSelect();
        $selector->setShowIncludes(false);
        $selector->setShowDeactivated(false);
        $selector->setName('template');

        $selector2 = new StylesheetSelect();
        $selector2->setName('stylesheet');

        $this->view->DESIGNS = $designs;
        $this->view->CREATE_URL =  $this->createLink('design', 'create');
        $this->view->NEW_NAME =  (isset($_POST[PARAM_DESIGN_NAME]) ? $_POST[PARAM_DESIGN_NAME]: '');
        $this->view->NEW_DESCRIPTION =  (isset($_POST['description']) ? $_POST['description']: '');
        $this->view->TEMPLATE = $selector->getHtml();
        $this->view->STYLESHEET = $selector2->getHtml();
    }

    /**
     * Edit a design.
     */
    public function editAction()
    {
        $tid = isset($_GET[PARAM_DESIGN_NAME]) ? $_GET[PARAM_DESIGN_NAME] : (
            isset($_POST[PARAM_DESIGN_NAME]) ? $_POST[PARAM_DESIGN_NAME] : ''
        );
        $temp = null;

        $ss = new Bigace_Smarty_Service();
        if ($tid != '') {
            $temp = $ss->getDesign($tid);
        }

        if ($temp === null) {
            $this->view->ERROR = getTranslation('missing_values');
            $this->_forward('index');
            return;
        }

        $template = $temp->getTemplate();
        $stylesheet = $temp->getStylesheet();

        $this->view->DESIGN = $temp;

        $selector = new TemplateSelect();
        $selector->setPreselected($template->getName());
        $selector->setShowIncludes(false);
        $selector->setShowDeactivated(false);
        $selector->setShowPreselectedIfDeactivated(true);
        $selector->setName('template');
        $this->view->TEMPLATE_SELECT =  $selector->getHtml();

        $selector2 = new StylesheetSelect();
        $selector2->setPreselected($stylesheet->getName());
        $selector2->setName('stylesheet');
        $this->view->STYLESHEET_SELECT =  $selector2->getHtml();

        if ($temp->hasPortletSupport()) {
            $this->view->PORTLET_SUPPORT =  ' checked="checked"';
        } else {
            $this->view->PORTLET_SUPPORT =  '';
        }
        $temp3 = $temp->getWidgetColumns();
        $pts = '';
        foreach($temp3 AS $name)
	        $pts .= ($pts == '' ? '' : ',') . $name;

        $temp3 = $temp->getContentNames();
        $ptn = '';
        foreach($temp3 AS $name)
	        $ptn .= ($ptn == '' ? '' : ',') . $name;

        $this->view->PORTLETS =  $pts;
        $this->view->CONTENTS =  $ptn;
        $this->view->NAME = $temp->getName();
        $this->view->DESCRIPTION = $temp->getDescription();
        $this->view->SAVE_URL = $this->createLink('design', 'update');
        $this->view->BACK_URL = $this->createLink('design');
    }

    /**
     * Updates an existing design.
     *
     * Expires the Page Cache.
     */
    public function updateAction()
    {
        $tid = isset($_GET[PARAM_DESIGN_NAME]) ? $_GET[PARAM_DESIGN_NAME] : (
            isset($_POST[PARAM_DESIGN_NAME]) ? $_POST[PARAM_DESIGN_NAME] : ''
        );
        $temp = null;

        $service = new Bigace_Smarty_Service();
        if ($tid != '') {
            $design = $service->getDesign($tid);
        }

        if (is_null($design)) {
            $this->view->ERROR = getTranslation('msg_saving_failed') . ' - wrong Design submitted!';
            $this->_forward('index');
            return;
        }

        if (isset($_POST['description']) && isset($_POST['stylesheet']) && isset($_POST['template'])) {
            $portletSupport = false;
            if (isset($_POST['portletSupport']) && $_POST['portletSupport'] == 1) {
                $portletSupport = true;
            }

            if ($service->updateDesign($tid, $_POST['description'], $_POST['template'], $_POST['stylesheet'], $portletSupport)) {
                $this->view->INFO = getTranslation('msg_saved');

                $portlets = array();
                $temp = explode(",", trim($_POST['portletColumns']));
                foreach ($temp as $entry) {
                	if(strlen(trim($entry)) > 0) //  && !isset($portlets[$entry])
                		$portlets[] = trim($entry);
                }
                $service->setPortlets($tid, $portlets);

                $contents = array();
                $temp = explode(",", trim($_POST['contents']));
                foreach ($temp AS $entry) {
                	$entry = trim($entry);
                	if (strlen($entry) > 0 && !isset($contents[$entry])) {
			            $entName = trim($entry);
			            $search = array("Ä","Ö","Ü","ä","ö","ü","ß","\t","\r","\n"," ");
			            $replace = array("AE","OE","UE","ae","oe","ue","ss","-","-","-","-");
			            $entName = str_replace($search, $replace, $entName);
			            // FIXME - what is delim ??? is it really a comma ???
			            $delim = ",";
			            $entName = preg_replace("/($delim)+/", $delim, preg_replace("/[^a-zA-Z0-9\/_-\\s]/", $delim, $entName));
                		$contents[] = $entName;
                	}
                }
                $service->setContents($tid, $contents);

                // expire page cache, a changed design could have an impact on page rendering
                Bigace_Hooks::do_action('expire_page_cache');

            } else {
                $this->view->ERROR = getTranslation('msg_saving_failed');
            }
        } else {
            $this->view->ERROR = getTranslation('missing_values');
        }
        $this->_forward('index');
    }

    public function createAction()
    {
        if (isset($_POST[PARAM_DESIGN_NAME]) && trim($_POST[PARAM_DESIGN_NAME]) != '' && isset($_POST['description'])
           && isset($_POST['template']) && isset($_POST['stylesheet'])) {
			$newName = trim($_POST[PARAM_DESIGN_NAME]);
            $design = new Bigace_Smarty_Design($newName);
            if ($design->getName() == $newName) {
                $this->view->ERROR = getTranslation('name_exists');
            } else {
				$service = new Bigace_Smarty_Service();
        		$template = new Bigace_Smarty_Template($_POST['template']);
        		$stylesheet = new Bigace_Smarty_Stylesheet($_POST['stylesheet']);
        		if ($template->getName() == $_POST['template'] && $stylesheet->getName() == $_POST['stylesheet']) {
                    $portletSupport = false;
                    if(isset($_POST['portletSupport']) && $_POST['portletSupport'] == 1)
                        $portletSupport = true;

                    if ($service->parseName($newName) != $newName) {
                        $this->view->ERROR = getTranslation('disallowed_character');
                    } else {
                        $service->createDesign(
                            $newName, $_POST['description'], $_POST['template'], $_POST['stylesheet'], $portletSupport
                        );
                    }
                } else {
                    $this->view->ERROR = 'Template or Stylesheet does not exist.';
                }
            }
        } else {
            $this->view->ERROR = getTranslation('missing_values');
        }
        $this->_forward('index');
    }

    /**
     * Deletes a design.
     *
     * This does not expire the page cache, because a removed design should not have any
     * influence on cached pages!
     */
    public function deleteAction()
    {
        if (isset($_GET[PARAM_DESIGN_NAME])) {
			$service = new Bigace_Smarty_Service();
			$design = $service->getDesign(urldecode($_GET[PARAM_DESIGN_NAME]));

			if (!is_null($design)) {
			  if ($service->countDesignUsage(urldecode($_GET[PARAM_DESIGN_NAME])) == 0) {
				  if ($service->deleteDesign(urldecode($_GET[PARAM_DESIGN_NAME]))) {
					  $this->view->INFO = getTranslation('msg_deleted');
				  } else {
					$this->view->ERROR = "Could not delete design";
				  }
			  } else {
				  $this->view->ERROR = getTranslation('delete_in_use');
			  }
			} else {
			  $this->view->ERROR = "Design does not exist.";
			}
        } else {
            $this->view->ERROR = getTranslation('missing_values');
        }
        $this->_forward('index');
    }

}