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
 * @version    $Id: CommentController.php 130 2011-03-16 17:57:44Z kevin $
 */

/**
 * Controller to create and display user comments.
 *
 * @category   Bigace
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class News_CommentController extends Bigace_Zend_Controller_Action
{
    const CONFIG_CSS = 'css.use.internal';
    /**
     * @var string
     */
    const CONFIG_EMAIL = 'email.required';
    /**
     * @var string
     */
    const CONFIG_GRAVATAR_ENABLE = 'gravatar.enable';
    /**
     * @var string
     */
    const CONFIG_GRAVATAR_SIZE = 'gravatar.size';
    /**
     * @var string
     */
    const CONFIG_HOMEPAGE = 'ask.for.homepage';
    /**
     * @var string
     */
    const CONFIG_NOFOLLOW = 'rel.nofollow';
    /**
     * @var string
     */
    const CONFIG_TITLE = 'title.display';
    /**
     * Comment list prefix.
     *
     * @var string
     */
    const CONFIG_LIST_PREFIX = 'comments.prefix';
    /**
     * Comment list prefix.
     *
     * @var string
     */
    const CONFIG_LIST_SUFFIX = 'comments.suffix';


    public function indexAction()
    {
        $req  = $this->getRequest();
        $item = $req->getParam('item');

        if ($item === null || !($item instanceof Bigace_Item)) {
            throw new Bigace_Exception('Parameter item must be instanceof Bigace_Item');
        }

        $service = new Bigace_Comment_Service();

        $display = Bigace_Comment_Service::isActivated();
        if ($display) {
            $display = $service->activeComments($item);
        }

        if (!$display) {
            return;
        }

        $all = array(
            self::CONFIG_TITLE           => true,
            self::CONFIG_CSS             => Bigace_Config::get("comments", "css.use.internal", true),
            self::CONFIG_EMAIL           => Bigace_Config::get("comments", "email.required", false),
            self::CONFIG_GRAVATAR_ENABLE => Bigace_Config::get("comments", "gravatar.enable", true),
            self::CONFIG_GRAVATAR_SIZE   => Bigace_Config::get("comments", "gravatar.size", "48"),
            self::CONFIG_HOMEPAGE        => Bigace_Config::get("comments", "ask.for.homepage", true),
            self::CONFIG_NOFOLLOW        => Bigace_Config::get("comments", "rel.nofollow", false),
            self::CONFIG_LIST_PREFIX     => '',
            self::CONFIG_LIST_SUFFIX     => '',
        );

        $params = $req->getUserParam('config', array());

        $configs = array();
        foreach ($all as $key => $value) {
            if (isset($params[$key])) {
                $configs[$key] = $params[$key];
            } else {
                $configs[$key] = $value;
            }
        }

        Bigace_Config::preload("comments");

        $this->view->showTitle     = $configs[self::CONFIG_TITLE];
        $this->view->useCSS        = $configs[self::CONFIG_CSS];
        $this->view->emailRequired = $configs[self::CONFIG_EMAIL];
        $this->view->useGravatar   = $configs[self::CONFIG_GRAVATAR_ENABLE];
        $this->view->gravatarSize  = $configs[self::CONFIG_GRAVATAR_SIZE];
        $this->view->useHomepage   = $configs[self::CONFIG_HOMEPAGE];
        $this->view->relnofollow   = $configs[self::CONFIG_NOFOLLOW];
        $this->view->commentPrefix = $configs[self::CONFIG_LIST_PREFIX];
        $this->view->commentSuffix = $configs[self::CONFIG_LIST_SUFFIX];

        $form = $this->getForm($item);

        if ($req->isPost() && $req->getPost('doComment') !== null) {

            $translator = Bigace_Translate::get('Zend_Validate');
            Zend_Validate_Abstract::setDefaultTranslator($translator);

            if ($form->isValid($_POST)) {

                $values   = $form->getValues();
                $comment  = $values['comment'];
                $name     = $values['name'];
                $email    = $values['email'];
                $homepage = $values['homepage'];

                if (strlen(trim($homepage)) > 0 && strpos($homepage, "http://") === false) {
                    $homepage = "http://" . $homepage;
                }

                if ($req->getPost('doComment') == 'create') {
                    $cas = new Bigace_Comment_Admin();
                    $r = $cas->createComment(
                        $item, $name,
                        $comment, $email, $homepage
                    );

                    $this->view->COMMENT_RESULT = $r;
                    $form->reset();
                    $form->populate(array('doComment' => 'create'));
                } else {
                    $this->view->COMMENT_RESULT = 'Wrong comment mode submitted';
                }

                // TODO support preview
                /*
                else {
                    $preview = array(
                        'name'      => $name,
                        'email'     => $email,
                        'homepage'  => $homepage,
                        'comment'   => $comment,
                        'ip'        => $_SERVER['REMOTE_ADDR'],
                        'timestamp' => time(),
                        'activated' => false,
                        'anonymous' => $GLOBALS['_BIGACE']['SESSION']->isAnonymous()
                    );
                    $result['mode'] = 'preview';
                }
                */
            }
        }

        //$form->setAction('/news/comment/add/');
        $this->view->COMMENT_FORM = $form;

        $this->view->COMMENTS = $service->getComments($item);
    }

    private function getForm(Bigace_Item $item)
    {
        $form = new Bigace_Comment_Form(array(), $item);
        $form->setAction('#commentform');

        return $form;
    }

}
