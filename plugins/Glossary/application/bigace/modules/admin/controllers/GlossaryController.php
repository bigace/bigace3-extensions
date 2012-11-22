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
 * @version    $Id: GlossaryController.php 4 2010-07-17 14:20:17Z kevin $
 */

/**
 * GlossaryController to handle the administration of Glossary entries.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_GlossaryController extends Bigace_Zend_Controller_Admin_Action
{

    // TODO 
    // make configurable whether HTML is allowed in descriptions or not

    public function initAdmin() 
    {
        if(!defined('GLOSSARY_CTRL'))
        {
            $this->addTranslation('glossary');
            
            define('GLOSSARY_CTRL', true);
        }
    }

    public function indexAction()
    {
        $bg = new Bigace_Glossary();
        
        $all = $bg->getAllEntries();
        $entries = array();
        
        foreach($all as $temp) 
        {
            $entries[] = array(
                'abbreviation' => $temp['abbreviation'],
                'description' => $temp['description'],
                'delete' => $this->createLink('glossary', 'delete', array('abbr' => urldecode($temp['abbreviation'])))
            );
        }

        $this->view->ADD_ACTION = $this->createLink('glossary', 'add');
        $this->view->ENTRIES = $entries;
    }
    
    // delete one link
    public function deleteAction()
    {
        $this->_forward('index');
        
        if(!isset($_GET['abbr'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }
        
        $bg = new Bigace_Glossary();
        try {
            $links = $bg->removeEntry($_GET['abbr']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_entry');
        }
    }

    // add a link
    public function addAction()
    {
        $this->_forward('index');
        
        if(!isset($_POST['abbr']))
        {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }
        
        $abbr = trim(strip_tags($_POST['abbr']));
        $desc = trim($_POST['description']);
        
        $bg = new Bigace_Glossary();
        try {
            $links = $bg->addEntry($abbr, $desc);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_creating_entry');
        }
    }

}