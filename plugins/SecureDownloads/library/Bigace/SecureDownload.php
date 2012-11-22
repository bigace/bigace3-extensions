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
 * @package    Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: SecureDownload.php 98 2011-03-15 10:10:43Z kevin $
 */

/**
 * Class handling all SecureDownload database requests.
 *
 * @category   Bigace
 * @package    Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_SecureDownload extends Bigace_Db_Table_Abstract
{
    protected $_name = 'secdwn';
    protected $_primary = array('cid', 'filename');

    /**
     * Returns the path where all files will be stored and searched in.
     *
     * @return String the absolute path to store secure download files in
     */
    public function getSecurePath()
    {
        if (!Zend_Registry::isRegistered('BIGACE_COMMUNITY')) {
            throw new Bigace_Exception('Community was null, cannot detect secure download folder.');
        }
        $community = Zend_Registry::get('BIGACE_COMMUNITY');
        $_folder   = $community->getPath() . 'secure/';

        // allow to overwrite the default path with a configured one
        $folder = Bigace_Config::get('secure', 'directory');
        if (!is_null($folder) && strlen(trim($folder)) > 2) {
            $_folder = trim($folder);
        }

        return $_folder;
    }

    /**
     * Adds a link ($link) to an existing file ($filename).
     * Make sure the file exists, will not be checked!
     * By default every link is only accessible once, can be changed by the $amount parameter.
     *
     * @param  string $filename the file to create the link for
     * @param  string $link the secure link which will can be accessed through the "secdwn" controller
     * @param  int    $expiry expiry must be a valid timestamp when the link will stop working
     * @param  int    $amount the amount of allowed downloads, before this links stops working
     * @return mixed  the primary key of the row inserted
     */
    public function addLinkForFile($filename, $link, $expiry, $amount = 1)
    {
        $data = array(
            'filename'  => $filename,
            'link'      => $link,
            'expiry'    => $expiry,
            'downloads' => $amount
        );

        return $this->insert($data);
    }

    /**
     * Returns an array of link arrays.
     *
     * @param  string $filename the file to get all links for
     * @return array  of link arrays
     */
    public function getLinksForFile($filename)
    {
        $select = $this->select()->where('filename = ?', $filename);

        $rows = $this->fetchAll($select);

        return $rows->toArray();
    }

    /**
     * Removes the given URL from the secure download list.
     *
     * @param string $url the download link to remove
     */
    public function removeLink($url)
    {
        $where = $this->getAdapter()->quoteInto('link = ?', $url);
        return $this->delete($where);
    }


    /**
     * Removes the given URL from the secure download list.
     *
     * @param string $url the download link to remove
     */
    public function removeAllLinks($filename)
    {
        $where = $this->getAdapter()->quoteInto('filename = ?', $filename);
        return $this->delete($where);
    }


    /**
     * Returns the link entry for a link.
     * Will return null if no link could be found.
     *
     * Make sure to check if the link is:
     * - expired
     * - has no more download slots (amount)
     *
     * Users should not be able to access such links.
     *
     * @param string $url the url to get the link entry for
     * @return array|null
     */
    public function getLink($url)
    {
        $select = $this->select()->where('link = ?', $url);

        $row = $this->fetchRow($select);

        if($row === null)
            return null;

        return $row->toArray();
    }

    /**
     * This method searches for expired links that need to be deleted.
     */
    public function cleanup()
    {
        return $this->delete('expiry <= '. time());
    }

}