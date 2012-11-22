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
 * Controller to render the jobs pages.
 *
 * Add the default CSS to your Layout:
 * <link rel="stylesheet" href="<?php echo $this->directory() . 'jobs/jobs.css'; ?>" type="text/css" media="screen, projection" />
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_JobsController extends Bigace_Zend_Controller_Page_Action
{

    /**
     * List all job types with all open jobs, links to details pages and
     * quick links to the detail documents.
     */
    public function indexAction()
    {
        $listing = array();

        $bjt = new Bigace_Jobs_Type();
        $types = $bjt->getAll();

        foreach ($types as $type) {
            $listing[$type['id']] = array(
                'id'     => $type['id'],
                'title'  => $type['title'],
                'url'    => LinkHelper::url(
                    'jobs/list/jid/'.$type['id'].'/'.urlencode($type['title'])
                ),
                'childs' => $this->getJobsForType($type['id'])
            );
        }

        $this->view->ENTRIES = $listing;
        $this->applyContent();
    }

    /**
     * List open jobs in the given type
     *
     * TODO:
     * - backlink to type listing
     */
    public function listAction()
    {
        $request = $this->getRequest();
        if ($request->getParam('jid') === null) {
            // show error message?
            $this->forward('index');
            return;
        }

        $bjt = new Bigace_Jobs_Type();
        $type = $bjt->get($request->getParam('jid'));

        if ($type === null) {
            // show error message?
            $this->forward('index');
            return;
        }

        $listing = array(
            'id' => $type['id'],
            'title' => $type['title'],
            'url' => LinkHelper::url('jobs/list/jid/'.$type['id'].'/'.urlencode($type['title'])),
            'childs' => $this->getJobsForType($type['id'])
        );

        $this->view->BACK_URL = LinkHelper::url('jobs/');
        $this->view->ENTRIES = $listing;
        $this->applyContent();
    }

    /**
     * Display the details of a job.
     *
     * TODO:
     * - backlink to type listing
     * - backlink to all jobs
     */
    public function detailsAction()
    {
        $request = $this->getRequest();
        if ($request->getParam('jid') === null) {
            // show error message?
            $this->forward('index');
            return;
        }

        $bj = new Bigace_Jobs();
        $job = $bj->get($request->getParam('jid'));

        if ($job === null) {
            // show error message?
            $this->forward('index');
            return;
        }

        $bjt = new Bigace_Jobs_Type();
        $type = $bjt->get($job['job_type']);

        $this->view->JOB = $this->getPreparedJob($job);
        $this->view->TYPE = array(
            'id'    => $type['id'],
            'title' => $type['title'],
            'url'   => LinkHelper::url('jobs/list/jid/'.$type['id'].'/'.urlencode($type['title']))
        );
        $this->view->BACK_URL = LinkHelper::url(
            'jobs/list/jid/'.$type['id'].'/'.urlencode($type['title'])
        );
        $this->applyContent();
    }

    /**
     * HELPER: Returns an array of prepared Job entries for the given type.
     */
    private function getJobsForType($type)
    {
        $all = array();

        $bj = new Bigace_Jobs();
        $jobs = $bj->getAllOpenForType($type);

        foreach ($jobs as $job) {
            $all[] = $this->getPreparedJob($job);
        }

        return $all;
    }

    /**
     * HELPER: Returns a preparep job, to be displayed in a view.
     */
    private function getPreparedJob($job)
    {
        $fs = new ItemService(_BIGACE_ITEM_FILE);

        $temp = array(
            'title' => $job['title'],
            'id' => $job['id'],
            'description' => $job['description'],
            'valid_to' => $job['valid_to'],
            'url' => LinkHelper::url('jobs/details/jid/'.$job['id'].'/'.urlencode($job['title']))
        );
        $doc = $fs->getItem($job['job_doc']);
        if($doc->exists())
            $temp['job_doc'] = $doc;

        $doc = $fs->getItem($job['person_doc']);
        if($doc->exists())
            $temp['person_doc'] = $doc;

        $doc = $fs->getItem($job['additional_doc']);
        if($doc->exists())
            $temp['additional_doc'] = $doc;

        return $temp;
    }

}