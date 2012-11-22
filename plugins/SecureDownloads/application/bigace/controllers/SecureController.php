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
 * @version    $Id: SecureController.php 4 2010-07-17 14:20:17Z kevin $
 */

/**
 * SecureController is able to send secured download files to the user.
 * If the file was sent completely the one-time link will be deleted.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_SecureController extends Bigace_Zend_Controller_Action
{

    public function __call($methodName, $args)
    {
        $request = $this->getRequest();
        $id = substr($methodName, 0, -6);

        $bsd = new Bigace_SecureDownload();
        $folder = $bsd->getSecurePath();
        
        // make sure all expired links/files will be purged
        $bsd->cleanup();

        $file = $bsd->getLink($id);
        if ($file === null) {
            throw new Bigace_Zend_Controller_Exception(
                array('message' => 'Could not find file with code ['.$id.']', 'code' => 404, 'script' => 'community'),
                array('backlink' => LinkHelper::url("/"))
            );
            return;
        }

        // not used currently, but if amount of download slots will be made configurable we need this check
        if ($file['downloads'] == 0) {
            throw new Bigace_Zend_Controller_Exception(
                array(
                    'message' => 'No more download slots available for file with code ['.$id.']',
                    'code' => 404, 'script' => 'community'
                ),
                array('backlink' => LinkHelper::url("/"))
            );
            return;
        }

        // should not happen as cleanup() was called before ... but one never knows ;)
        if ($file['expiry'] < time()) {
            throw new Bigace_Zend_Controller_Exception(
                array(
                    'message' => 'Sorry, link expired for file with code ['.$id.']',
                    'code' => 404, 'script' => 'community'
                ),
                array('backlink' => LinkHelper::url("/"))
            );
            return;
        }
            
        $fullUrl = $folder . $file['filename'];
        
        $this->getResponse()
            ->setHeader('Content-Type', $this->getMimeType($fullUrl), true)
            ->setHeader('Content-Disposition', 'inline; filename='.$file['filename'], true)
	        ->sendHeaders();  

        readfile($fullUrl);
        
        $bsd->removeLink($id);
        
        // check if we should delete files without links
        if (Bigace_Config::get("secure", "delete.files.without.links", true)) {
            $links = $bsd->getLinksForFile($file['filename']);
            if (count($links) == 0) {
                // delete file itself
                if (!unlink($fullUrl)) {
                    // TODO write log message
                }
            }
        }
        
        $this->getResponse()->clearAllHeaders();

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->removeHelper('viewRenderer');
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
    }


    private function getMimeType($filename, $magicfile = null)
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type($filename);
        }

        if (function_exists('finfo_open')) {
            if($magicfile !== null)
                $finfo = finfo_open(FILEINFO_MIME, $magicfile);
            else
                $finfo = finfo_open(FILEINFO_MIME);
                
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        // TODO some fallback, exception and/or log ???
        return null;
    }
    
}
