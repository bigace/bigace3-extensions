<?php
/*
Plugin Name: Comments Plugin
Plugin URI: http://wiki.bigace.de/bigace:extensions:addon:comments
Description: This plugins brings per-page configuration for comments, trackback support, an administration page and a template include to display comment forms. Please read the <a href="http://wiki.bigace.de/bigace:extensions:addon:comments" target="_blank">Comments Documentation</a> on how to install and configure this Plugin properly.
Author: Keleo
Version: 2.0
Author URI: http://www.keleo.de/
$Id: Comments.php 193 2011-04-11 09:30:02Z kevin $
*/
class Plugin_Comments implements Bigace_Plugin
{

    public function init()
    {
        // trackbacks are not ready yet
        // Bigace_Hooks::add_action('page_header', array($this, 'getPageheader'), 1);
        // Bigace_Hooks::add_filter('metatags_more', array($this, 'getMetatags'), 10, 2);

        Bigace_Hooks::add_filter('admin_menu', array($this, 'getAdminMenu'), 10, 2);
        Bigace_Hooks::add_filter('admin_portlets', array($this, 'getAdminPortlets'), 10, 3);
        Bigace_Hooks::add_filter('credits', array($this, 'getAdminCredits'), 10, 1);
        Bigace_Hooks::add_filter('edit_item_meta', array($this, 'comment_item_attributes'), 10, 2);
        Bigace_Hooks::add_filter('create_item_meta', array($this, 'comment_create_item_attributes'), 10, 2);
        Bigace_Hooks::add_action('init_view', array($this, 'initView'), 10, 1);

        Bigace_Hooks::add_action('comments_plugin', array($this, 'getVersion'), 10, 1);
        Bigace_Hooks::add_action('delete-item', array($this, 'deleteItem'), 10, 3);
        Bigace_Hooks::add_action('update_item', array($this, 'update_comment_item'), 10, 5);

        Bigace_Comment_Service::setActivated(true);
    }

    /**
     * FIXME 3.0 activate after fixing trackback
     */
    public function getMetatags($values, $item)
    {
        if ($item->getParentID() == $this->rootID) {
            $values[] = '<link rel="pingback" href="'.BIGACE_HOME.'xmlrpc.php" />';
        }
        return $values;
    }

    /**
     * FIXME 3.0 activate after fixing trackback
     */
    public function getPageheader($item)
    {
        if ($item->getParentID() == $this->rootID || $item->getID() == $this->rootID) {
            header( 'X-Pingback: ' . BIGACE_HOME.'xmlrpc.php' );
        }
    }

    public function getVersion()
    {
        return "2.0";
    }

    public function getAdminCredits(array $credits)
    {
        $credits['Comments'] = array(
            'Comments' => array(
                'title' => 'Comments',
                'weblink' => "http://wiki.bigace.de/bigace:extensions:addon:comments",
                'copyright' => "Keleo - Kevin Papst",
                'description' => "Comments is an addon to the news system, " .
                                "bringing a blog-like feeling to your page. You can globally " .
                                "turn comments off and on by de-/activating this plugin."
            )
        );
        return $credits;
    }

    /**
     * Adds news RSS links to the View header.
     *
     * @param Zend_View $view
     */
    public function initView(Zend_View $view)
    {
        $showFeed = Bigace_Config::get('comments', 'feeds.enable', true);
        if ($showFeed === false) {
            return;
        }

        $feedUrl = Bigace_Config::get('comments', 'feeds.custom.url', '');

        // a basic url needs more than 6 character, but if the user enters at least a
        // protocol, he gets what he wants ;)
        if (strlen(trim($feedUrl)) > 6) {
            $view->headLink(
                array(
                    'rel'   => 'alternate',
                    'href'  => $feedUrl,
                    'title' => 'Comments',
                    'type'  => 'application/rss+xml'
                )
            );

            return;
        }

        $view->headLink(
            array(
                'rel'   => 'alternate',
                'href'  => LinkHelper::url("news/feed/comments/type/rss"),
                'title' => 'Comments (RSS 2.0)',
                'type'  => 'application/rss+xml'
            )
        );

        $view->headLink(
            array(
                'rel'   => 'alternate',
                'href'  => LinkHelper::url("news/feed/comments/"),
                'title' => 'Comments (Atom)',
                'type'  => 'application/atom+xml'
            )
        );
    }

    /**
     * activates the comment admin screen
     */
    public function getAdminMenu(array $menu, Bigace_Zend_Controller_Admin_Action $controller)
    {
        $controller->addTranslation('comments');

        $menu['addon']['childs']['comments'] = array(
                'permission' => 'comments'
        );

        return $menu;
    }

    // adds the comment portlet to the dashboard
    public function getAdminPortlets(array $portlets, $name, Bigace_Zend_Controller_Admin_Action $portletController)
    {
        if ($name == 'index' || $name == 'addon') {
            $portlets = array_merge(
                array(new Bigace_Admin_Portlet_Comments($portletController)), $portlets
            );
        }

        return $portlets;
    }

    public function deleteItem($itemtype, $id, $language = null)
    {
        $cas = new Bigace_Comment_Admin($itemtype);
        if ($language !== null) {
            $cas->deleteAll($itemtype, $id, $language);
        } else {
            $cas->deleteAll($itemtype, $id);
        }
    }

    public function comment_create_item_attributes($values, $itemtype)
    {
        if ($itemtype == _BIGACE_ITEM_MENU) {
            $allowTrackbacks = Bigace_Config::get("comments", "allow.trackbacks", true);

            loadLanguageFile('comments');

            $values[getTranslation('comment.admin.title')] = $this->showAdminTable(true, $allowTrackbacks, $allowTrackbacks);
        }

        return $values;
    }

    public function comment_item_attributes($values, $item)
    {
        if ($item->getItemtypeID() == _BIGACE_ITEM_MENU) {
            $cs = new Bigace_Comment_Service();
            $commentsAllowed = $cs->activeComments($item);
            $trackbacksAllowed = $cs->activeRemoteComments($item);
            $allowTrackbacks = Bigace_Config::get("comments", "allow.trackbacks", true);

            loadLanguageFile('comments');

            $values[getTranslation('comment.admin.title')] = $this->showAdminTable(
                $commentsAllowed, $allowTrackbacks, $trackbacksAllowed
            );
        }

        return $values;
    }

    private function showAdminTable($commentsAllowed, $trackbackEnabled = false, $trackbacksAllowed = false)
    {
        $html = '
            <table border="0">
                <col width="170"/>
                <col />
                <tr>
                    <td valign="top">'.getTranslation('comment.admin.enable').'</td>
                    <td>
                        <select name="allow_comments">
                            <option value="'.intval(true).'"'.($commentsAllowed ? ' selected' : '').'>'.getTranslation('comment_yes').'</option>
                            <option value="'.intval(false).'"'.(!$commentsAllowed ? ' selected' : '').'>'.getTranslation('comment_no').'</option>
                        </select>
                    </td>
            </tr>';

        if ($trackbackEnabled) {
            $html .= '
                <tr>
                    <td valign="top">'.getTranslation('comment.admin.trackback').'</td>
                    <td>
                        <select name="allow_trackbacks">
                            <option value="'.intval(true).'"'.($trackbacksAllowed ? ' selected' : '').'>'.getTranslation('comment_yes').'</option>
                            <option value="'.intval(false).'"'.(!$trackbacksAllowed ? ' selected' : '').'>'.getTranslation('comment_no').'</option>
                        </select>
                    </td>
                </tr>';
        }

        $html .= '
               </table>
        ';

        return $html;
    }

    // update item which was submitted with the general item attribute admin screen
    public function update_comment_item($itemtype, $id, $langid, $val, $timestamp)
    {
        if (isset($_POST['allow_comments']) || isset($_POST['allow_trackbacks'])) {
            $item = Bigace_Item_Basic::get(_BIGACE_ITEM_MENU, $id, $langid);
            if ($item === null) {
                return;
            }

            $project = new Bigace_Item_Project_Numeric();
            if (isset($_POST['allow_comments'])) {
                $project->save(
                    $item, 'allow_comments', Bigace_Util_Sanitize::integer($_POST['allow_comments'])
                );
            }
            if (isset($_POST['allow_trackbacks'])) {
                $project->save(
                    $item, 'allow_trackbacks', Bigace_Util_Sanitize::integer($_POST['allow_trackbacks'])
                );
            }
        }
    }

    public function activate()
    {
        return true;
    }

    public function deactivate()
    {
    }

}