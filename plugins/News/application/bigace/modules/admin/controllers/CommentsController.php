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
 * @version    $Id: CommentsController.php 193 2011-04-11 09:30:02Z kevin $
 */

/**
 * Script for Comment management.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_CommentsController extends Bigace_Zend_Controller_Admin_Action
{
    private $_canEdit = null;
    private $_canActivate = null;
    private $_canDelete = null;

    public function initAdmin()
    {
        if (!defined('COMMENTS_CTRL')) {
            $this->addTranslation('comments');
        }

        if ($this->_canEdit === null) {
            $this->_canEdit = has_permission('comments');
            $this->_canActivate = $this->_canEdit;
            $this->_canDelete = $this->_canEdit;
        }
    }

    public function indexAction()
    {
        if (!$this->_canEdit) {
            $this->view->ERROR = getTranslation('error_permission');
            return;
        }

        $cs = new Bigace_Comment_Service();
        $this->view->LISTING_URL = $this->createLink('comments');

        $this->view->PERM_ACTIVATE = $this->_canActivate;
        $this->view->PERM_DELETE = $this->_canDelete;
        $this->view->PERM_EDIT = $this->_canEdit;
        $this->view->TITLE = getTranslation('comments.pending');

        // starting counter
        $start = isset($_POST['limitFrom']) ? intval($_POST['limitFrom']) : 1;
        // amount of comments per page
        $end = isset($_POST['limitTo']) ? intval($_POST['limitTo']) : 10;
        $totalItems = 0;

        $startComment = ($start-1) * $end;
        // check if pending comments are available
        $values = array('START' => $startComment, 'END' => $end);
        $sqlString = "SELECT * FROM {DB_PREFIX}comments WHERE `cid` = {CID}
                        AND `activated` = '0'
                        ORDER BY `timestamp` ASC LIMIT {START}, {END}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        // count all pending comments
        if ($res->count() > 0) {
            $totalItems = $cs->countPending();
        }

        $comments = array();

        // if no pending comments are available, show activated ones
        if ($res->count() == 0) {
            $this->view->TITLE = getTranslation('comments.activated');
        	$this->view->PERM_ACTIVATE = false;
        	$sqlString = "SELECT * FROM {DB_PREFIX}comments WHERE
        	                `cid` = {CID} ORDER BY `timestamp` DESC LIMIT {START}, {END}";
            $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
            $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

            // prepare zend_paginator for activated comments
            if ($res->count() > 0) {
                $totalItems = $cs->countActivated();
            }
        }

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalItems));
        $paginator->setItemCountPerPage($end);
        if ($start <= 0) {
            $paginator->setCurrentPageNumber(1);
        } else {
            $paginator->setCurrentPageNumber($start);
        }

        for ($i=0; $i < $res->count(); $i++) {
            $c = $res->next();
        	$comments[] = array(
        	    'id' => $c['id'],
        	    'name' => $c['name'],
        	    'homepage' => $c['homepage'],
        	    'email' => $c['email'],
        	    'anonymous' => $c['anonymous'],
        	    'ip' => $c['ip'],
        	    'timestamp' => $c['timestamp'],
        	    'comment' => $c['comment'],
        	    'edit' => $this->createLink('comments', 'edit', array('id' => $c['id'])),
        	    'spam' => $this->createLink('comments', 'spam', array('id' => $c['id'])),
        	    'delete' => $this->createLink('comments', 'delete', array('id' => $c['id'])),
        	    'activate' => $this->createLink('comments', 'activate', array('id' => $c['id'])),
        	);
        }

        $this->view->LIMIT_FROM = $start;
        $this->view->LIMIT_TO = $end;
        $this->view->PAGINATOR = $paginator;
        $this->view->COMMENTS = $comments;
    }

    /**
     * edit existing entry
     */
    public function editAction()
    {
        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            $this->_forward('index');
            return;
        }

        if (!$this->_canEdit) {
            $this->view->ERROR = getTranslation('error_permission');
            $this->_forward('index');
            return;
        }

        $id = intval($_GET['id']);

	    $sqlString = "SELECT * FROM {DB_PREFIX}comments WHERE `cid` = {CID} AND `id` = {ID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        $comment = $res->next();

	    $this->view->BACK_URL = $this->createLink('comments');
	    $this->view->SAVE_URL = $this->createLink('comments', 'update');
	    $this->view->COMMENT = $comment;
	    $this->render('edit');
    }

    /**
     * activate existing entry
     */
    public function activateAction()
    {
        $this->_forward('index');

        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        if (!$this->_canActivate) {
            $this->view->ERROR = getTranslation('error_permission');
            return;
        }

        $cas = new Bigace_Comment_Admin();
        $rr = $cas->activate(intval($_GET['id']));

        if ($rr === false) {
            $this->view->ERROR = getTranslation('error_activation');
        }
    }

    /**
     * save/update existing entry
     */
    public function updateAction()
    {
        $this->_forward('index');

        if (!isset($_POST['commentID'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        if (!$this->_canEdit) {
            $this->view->ERROR = getTranslation('error_permission');
            return;
        }

        $cas = new Bigace_Comment_Admin();
        $rr = $cas->update(
            intval($_POST['commentID']),
            Bigace_Util_Sanitize::plaintext($_POST['name']),
            Bigace_Util_Sanitize::html($_POST['comment']),
            Bigace_Util_Sanitize::email($_POST['email']),
            Bigace_Util_Sanitize::plaintext($_POST['homepage'])
        );

        if ($rr === false) {
            $this->view->ERROR = getTranslation('error_updating');
        }
    }

    /**
     * delete existing entry
     */
    public function deleteAction()
    {
        $this->_forward('index');

        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        if (!$this->_canDelete) {
            $this->view->ERROR = getTranslation('error_permission');
            return;
        }

        $cas = new Bigace_Comment_Admin();
        $rr = $cas->delete(intval($_GET['id']));

        if ($rr === false) {
            $this->view->ERROR = getTranslation('error_deleting');
        }
    }

    /**
     * mark existing entry as spam and delete it
     */
    public function spamAction()
    {
        $this->_forward('index');

        // delete existing entry
        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        if (!$this->_canDelete) {
            $this->view->ERROR = getTranslation('error_permission');
            return;
        }

        $cas = new Bigace_Comment_Admin();
        $rr = $cas->deleteSpam(intval($_GET['id']));
        if ($rr === false) {
            $this->view->ERROR = getTranslation('error_deleting');
        }
    }

}
