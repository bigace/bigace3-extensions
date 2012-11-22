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
 * @package    Bigace_Admin
 * @subpackage Portlet
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Comments.php 193 2011-04-11 09:30:02Z kevin $
 */

/**
 * A portlet displaying some basic statistics about received comments.
 *
 * @category   Bigace
 * @package    Bigace_Admin
 * @subpackage Portlet
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Admin_Portlet_Comments extends Bigace_Admin_Portlet_Default
{

    public function getFilename()
    {
        return 'portlets/comments.phtml';
    }

    public function getParameter()
    {
        $cs = new Bigace_Comment_Service();

        return array(
            'PENDING'    => $cs->countPending(),
            'ACTIVATED'  => $cs->countActivated(),
            'SPAM'       => $cs->countSpam(),
            'ACTION_URL' => $this->createLink('index', 'comments')
        );
    }

    public function render()
    {
        return has_permission('comments');
    }

}
