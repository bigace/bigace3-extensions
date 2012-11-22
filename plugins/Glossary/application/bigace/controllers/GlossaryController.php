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
 * Controller to render the glossary entries.
 * 
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_GlossaryController extends Bigace_Zend_Controller_Page_Action
{
    
    public function indexAction() 
    {
        $bg = new Bigace_Glossary();
        $all = $bg->getAllEntries();
        $this->view->ENTRIES = $all;
        
        $this->applyContent();
    }

}
