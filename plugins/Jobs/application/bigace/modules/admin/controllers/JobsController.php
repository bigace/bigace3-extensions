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
 * @version    $Id: JobsController.php 140 2011-03-17 19:18:33Z kevin $
 */

/**
 * JobsController to handle the administration of Job vacancies.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Admin_JobsController extends Bigace_Zend_Controller_Admin_Action
{

    private $expiryDefault = "+1 week";

    public function initAdmin()
    {
        $this->addTranslation('jobs');
    }

    private function getAllJobTypes()
    {
        $bjt = new Bigace_Jobs_Type();
        $allJobTypes = $bjt->getAll();
        $allTypes = array();
        foreach($allJobTypes as $t)
            $allTypes[$t['id']] = $t['title'];

        return $allTypes;
    }

    /**
     * Display the index screen.
     */
    public function indexAction()
    {
        $bj = new Bigace_Jobs();

        $allTypes = $this->getAllJobTypes();

        $fs = new ItemService(_BIGACE_ITEM_FILE);

        $all = $bj->getAllOpen();
        $entriesOpen = array();

        foreach ($all as $temp) {
            $entriesOpen[] = array(
                'id' => $temp['id'],
                'name' => $temp['title'],
                'description' => $temp['description'],
                'valid_to' => $temp['valid_to'],
                'type_id' => $temp['job_type'],
                'type' => $allTypes[$temp['job_type']],
                'job_doc' => $fs->getItem($temp['job_doc']),
                'person_doc' => $fs->getItem($temp['person_doc']),
                'additional_doc' => $fs->getItem($temp['additional_doc']),
                'delete' => $this->createLink('jobs', 'delete', array('id' => $temp['id'])),
                'edit' => $this->createLink('jobs', 'edit', array('id' => $temp['id']))
            );
        }

        $all = $bj->getAllClosed();
        $entriesClosed = array();

        foreach ($all as $temp) {
            $entriesClosed[] = array(
                'id' => $temp['id'],
                'name' => $temp['title'],
                'description' => $temp['description'],
                'valid_to' => $temp['valid_to'],
                'type_id' => $temp['job_type'],
                'type' => $allTypes[$temp['job_type']],
                'job_doc' => $fs->getItem($temp['job_doc']),
                'person_doc' => $fs->getItem($temp['person_doc']),
                'additional_doc' => $fs->getItem($temp['additional_doc']),
                'delete' => $this->createLink('jobs', 'delete', array('id' => $temp['id'])),
                'edit' => $this->createLink('jobs', 'edit', array('id' => $temp['id']))
            );
        }

        $this->view->ALL_OPEN   = $entriesOpen;
        $this->view->ALL_CLOSED = $entriesClosed;
        $this->view->ACTION_NEW = $this->createLink('jobs', 'new');
        $this->view->ACTION_JOBTYPES = $this->createLink('jobs', 'jobtypes');
    }

    /**
     * Display the new Job form.
     */
    public function newAction()
    {
        $allTypes = $this->getAllJobTypes();
        $expiry   = strtotime($this->expiryDefault, time());
        if (isset($_POST['expiry'])) {
            $expiry = strtotime($_POST['expiry']);
        }

        $this->view->ALL_TYPES   = $allTypes;
        $this->view->DESCRIPTION = isset($_POST['description']) ? $_POST['description'] : '';
        $this->view->NAME        = isset($_POST['name']) ? $_POST['name'] : '';
        $this->view->EXPIRY      = $expiry;
        $this->view->TYPE        = isset($_POST['type']) ? $_POST['type'] : '';
        $this->view->TITLE       = 'new_job_legend';
        $this->view->ACTION      = $this->createLink('jobs', 'save');
        $this->view->BACK_URL    = $this->createLink('jobs', 'index');
        $this->view->JOB_DOC     = null;
        $this->view->PERSON_DOC  = null;
        $this->view->ADDITIONAL_DOC = null;

        $this->render('form');
    }

    /**
     * Adds or updates a job entry.
     */
    public function saveAction()
    {

        if (!isset($_POST['name']) || strlen(trim($_POST['name'])) == 0 ||
            !isset($_POST['description']) ||
            !isset($_POST['expiry'])) {
            $this->view->ERROR = getTranslation('missing_values');
            $this->_forward('new');
            return;
        }

        $title       = trim($_POST['name']);
        $typeId      = $_POST['type'];
        $description = trim($_POST['description']);
        $validTo     = strtotime($_POST['expiry']);
        $personDocId = $_POST['personDoc'];
        $jobDocId    = $_POST['jobDoc'];
        $addDocId    = $_POST['additionalDoc'];

        $bj = new Bigace_Jobs();
        try {
            if (isset($_POST['id'])) {
                $res = $bj->save(
                    $_POST['id'], $title, $typeId, $description,
                    $validTo, $jobDocId, $personDocId, $addDocId
                );
            } else {
                $res = $bj->add(
                    $title, $typeId, $description, $validTo,
                    $jobDocId, $personDocId, $addDocId
                );
            }
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_creating_entry') . ':' . $e->getMessage();
        }

        $this->_forward('index');
    }

    /**
     * Deletes one job.
     */
    public function deleteAction()
    {
        $this->_forward('index');

        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bj = new Bigace_Jobs();
        try {
            $bj->remove($_GET['id']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_entry') . ':' . $e->getMessage();
        }
    }

    public function editAction()
    {
        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            $this->_forward('index');
            return;
        }

        $allTypes = $this->getAllJobTypes();
        $this->view->ALL_TYPES = $allTypes;

        $bj = new Bigace_Jobs();
        $job = $bj->get($_GET['id']);

        if ($job === null) {
            $this->view->ERROR = getTranslation('failed_loading_entry');
            $this->_forward('index');
            return;
        }

        $this->view->DESCRIPTION = $job['description'];
        $this->view->NAME = $job['title'];
        $this->view->EXPIRY = $job['valid_to'];
        $this->view->TYPE = $job['job_type'];
        $this->view->ID = $job['id'];

        $fs = new ItemService(_BIGACE_ITEM_FILE);
        $this->view->JOB_DOC = $fs->getItem($job['job_doc']);
        $this->view->PERSON_DOC = $fs->getItem($job['person_doc']);
        $this->view->ADDITIONAL_DOC = $fs->getItem($job['additional_doc']);

        $this->view->TITLE = 'update_job_legend';
        $this->view->ACTION = $this->createLink('jobs', 'save');
        $this->view->BACK_URL = $this->createLink('jobs', 'index');

        $this->render('form');
    }

    /**
     * Display the JobType action screen.
     */
    public function jobtypesAction()
    {
        $allTypes = $this->getAllJobTypes();
        $all = array();
        $bj = new Bigace_Jobs();

        foreach ($allTypes as $id => $name) {
            $delete = null;
            $counter = $bj->getByType($id);
            if ($counter === null || count($counter) == 0) {
                $delete = $this->createLink('jobs', 'typedelete', array('id' => $id));
            }

            $all[] = array(
                'id' => $id,
                'name' => $name,
                'update' => $this->createLink('jobs', 'typeupdate'),
                'delete' => $delete
            );
        }

        $this->view->ALL_TYPES = $all;
        $this->view->ACTION = $this->createLink('jobs', 'typecreate');
        $this->view->BACK_URL = $this->createLink('jobs', 'index');
    }

    public function typeupdateAction()
    {
        $this->_forward('jobtypes');

        if (!isset($_POST['name']) || !isset($_POST['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bjt = new Bigace_Jobs_Type();
        try {
            $bjt->save($_POST['id'], $_POST['name']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_updating_entry') . ':' . $e->getMessage();
        }
    }

    public function typecreateAction()
    {
        $this->_forward('jobtypes');

        if (!isset($_POST['name']) || strlen(trim($_POST['name'])) == 0) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bjt = new Bigace_Jobs_Type();
        try {
            $bjt->add($_POST['name']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_creating_entry') . ':' . $e->getMessage();
        }
    }

    public function typedeleteAction()
    {
        $this->_forward('jobtypes');

        if (!isset($_GET['id'])) {
            $this->view->ERROR = getTranslation('missing_values');
            return;
        }

        $bj = new Bigace_Jobs();
        $counter = $bj->getByType($_GET['id']);

        if ($counter !== null && count($counter) > 0) {
            // user manipulated URL
            $this->view->ERROR = getTranslation('failed_removing_entry') . ' - already in use...';
            return;
        }

        $bjt = new Bigace_Jobs_Type();
        try {
            $bjt->remove($_GET['id']);
        } catch (Exception $e) {
            $this->view->ERROR = getTranslation('failed_removing_entry') . ':' . $e->getMessage();
        }
    }
}
