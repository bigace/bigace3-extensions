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
 * @version    $Id: SecureController.php 24 2010-12-14 10:57:28Z kevin $
 */

/**
 * SecureController to administrate secure download files and links.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_SecureController extends Bigace_Zend_Controller_Admin_Action
{
    // TODO
    // make configurable if first link should be auto created
    // make default time configurable
    // make default amount configurable
    // make length of random string configurable

    private $_folder;
    private $_defaultTime = "+1 week";
    private $_defaultAmount = 1;

    public function initAdmin()
    {
        if(!defined('SECURE_CTRL'))
        {
            $this->addTranslation('secure');

            define('SECURE_CTRL', true);

            $this->view->ACTION = 'credits';
        }

        $bsd = new Bigace_SecureDownload();
        $this->_folder = $bsd->getSecurePath();

        if(!file_exists($this->_folder) || !is_dir($this->_folder) || !is_writable($this->_folder)) {
            if(!IOHelper::createDirectory($this->_folder)) {
                $this->view->ERROR = sprintf(getTranslation('problems_basepath'), $this->_folder);
                $this->view->HIDE_ALL = true;
            }
        }
    }

    public function indexAction()
    {
        import('classes.util.IOHelper');

        $bsd = new Bigace_SecureDownload();

        // make sure all expired links/files will be purged
        $bsd->cleanup();

        $allFiles = array();

        foreach (glob($this->_folder . "*.*") as $filename)
        {
            $pp = pathinfo($filename);
            $id = urlencode($pp['basename']);

            $links = $bsd->getLinksForFile($pp['basename']);
            $prepLinks = array();

            if(count($links) == 0)
            {
                $delete = Bigace_Config::get("secure", "days.before.removal", "7");
                // if days.before.removal == -1 never remove files automatically
                if($delete > -1) {
                    try {
                        $expiryTime = strtotime("+".$delete." days", filectime($filename));
                        // delete expired files
                        if($expiryTime < time()) {
                            if(!unlink($filename)) {
                                $this->view->ERROR = getTranslation('failed_removing_file');
                            } else {
                                continue;
                            }
                        }
                    } catch (Exception $e) {
                        $this->view->ERROR = getTranslation('failed_removing_file');
                    }

                }
            }

            foreach($links as $le) {
                $prepLinks[] = array(
                    'expiry' => $le['expiry'],
                    'url' => $le['link'],
                    'delete' => $this->createLink('secure', 'delete', array('url' => urlencode($le['link'])))
                );
            }

            $allFiles[] = array(
                'id' => $id,
                'base' => LinkHelper::url("secure/"),
                'path' => $filename,
                'name' => $pp['basename'],
                'size' => filesize($filename),
                'link' => $prepLinks,
                'delete' => $this->createLink('secure', 'remove', array('id' => $id)),
                'truncate' => $this->createLink('secure', 'truncate', array('id' => $id)),
            );
        }

        $this->view->ADD_LINKS = $this->createLink('secure', 'add');
        $this->view->UPLOAD_ACTION = $this->createLink('secure', 'upload');
        $this->view->FILES = $allFiles;
        $this->view->FILESIZE = ini_get("upload_max_filesize");
        $this->view->FOLDER = $this->_folder;
    }

    // adds a new file
    public function uploadAction()
    {
        $this->_forward('index');

        if(!isset($_FILES['securefile'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        if(!move_uploaded_file($_FILES['securefile']['tmp_name'], $this->_folder . $_FILES['securefile']['name']))
        {
            throw new Exception("Failed moving " . $_FILES['securefile']['name'] . " to SecureDownload area");
        }

        // if upload worked, create a default link
        $amount = $this->_defaultAmount;
        $link = Bigace_Util_Random::getRandomString();
        $expiry = strtotime($this->_defaultTime, time());

        $bsd = new Bigace_SecureDownload();
        try {
            $bsd->addLinkForFile($_FILES['securefile']['name'], urlencode($link), $expiry, $amount);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_creating_link');
        }
    }

    // delete one link
    public function deleteAction()
    {
        $this->_forward('index');

        if(!isset($_GET['url'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bsd = new Bigace_SecureDownload();
        try {
            $links = $bsd->removeLink($_GET['url']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_link');
        }
    }

    // delete all links
    public function truncateAction()
    {
        $this->_forward('index');

        if(!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bsd = new Bigace_SecureDownload();
        try {
            $links = $bsd->removeAllLinks($_GET['id']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_link');
        }
    }

    // add a link
    public function addAction()
    {
        $this->_forward('index');

        if(!isset($_POST['filename']))
        {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $amount = isset($_POST['amount']) ? $_POST['amount'] : $this->_defaultAmount;
        $link = (isset($_POST['url']) && strlen(trim($_POST['url'])) > 0) ? $_POST['url'] : Bigace_Util_Random::getRandomString();
        $expiry = isset($_POST['expiry']) ? strtotime($_POST['expiry']) : strtotime($this->_defaultTime, time());

        if($expiry == -1 || $expiry < time())
            $expiry = strtotime($this->_defaultTime, time());

        $bsd = new Bigace_SecureDownload();
        try {
            $links = $bsd->addLinkForFile($_POST['filename'], urlencode($link), $expiry, $amount);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_creating_link');
        }
    }

    // remove file and links
    public function removeAction()
    {
        $this->_forward('index');

        if(!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bsd = new Bigace_SecureDownload();
        try {
            $links = $bsd->removeAllLinks($_GET['id']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_link');
        }

        try {
            if(!unlink($this->_folder . $_GET['id']))
                $this->view->ERROR = getTranslation('failed_removing_file');
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_file');
        }
    }

}
