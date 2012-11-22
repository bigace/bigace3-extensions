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
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id: Admin.php 24 2010-12-14 10:57:28Z kevin $
 */

import('classes.util.IdentifierHelper');
import('classes.email.TextEmail');

/**
 * Class used for administrating "Comments"
 *
 * @category   Bigace
 * @package    Bigace_Comment
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Comment_Admin
{
    const SPAM = 'spam'; // flagged as spam
    const DUPLICATE = 'duplicate'; // duplicate detected
    const NOT_CREATED = 'notcreated'; // error
    const AWAIT_MODERATION = 'moderation'; // waiting for approval
    const CREATED = 'created'; // published

    private function checkIsSpam($apiKey, Bigace_Item $item, $name, $comment, $email = '', $homepage = '')
    {
        import('classes.item.ItemService');

        require_once(BIGACE_3RDPARTY.'akismet.class.php');
        $comment = array(
            'author'    => $name,
            'email'     => $email,
            'website'   => $homepage,
            'body'      => $comment,
            'permalink' => LinkHelper::itemUrl($item)
         );

         $akismet = new Akismet(BIGACE_HOME, trim($apiKey), $comment);

         if ($akismet->errorsExist()) {
            $errs = $akismet->getErrors();
            $err = "";
            foreach ($errs as $errName => $errMsg) {
                $err .= " [".$errName."] " . $errMsg;
            }
            $GLOBALS['LOGGER']->logError('Could not connect to Askimet Server: ' . $err);
         } else {
            return $akismet->isSpam();
         }
         return false;
    }

    function createComment(Bigace_Item $item, $name, $comment, $email = '', $homepage = '', $type = 'comment')
    {
        $activate = false;
        if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
            $activate = Bigace_Config::get("comments", "auto.activate.unregistered", false);
        else
            $activate = Bigace_Config::get("comments", "auto.activate.registered", false);

        $apiKey = Bigace_Config::get("comments", "akismet.api.key");

        if ($apiKey !== null && strlen(trim($apiKey)) > 0) {
            $autoDelete = Bigace_Config::get("comments", "akismet.auto.delete.negative", false);
            $autoActivate = Bigace_Config::get("comments", "akismet.auto.activate.positive", true);
            if ($autoActivate || $autoDelete) {
                $spam = $this->checkIsSpam($apiKey, $item, $name, $comment, $email, $homepage);
                if ($spam) {
                    $activate = false;
                    if ($autoDelete) {
                        $this->increaseSpamCounter();
                        return self::SPAM;
                    }
                } else {
                    if($autoActivate)
                        $activate = true;
                }
            }
        }

        $cs = new Bigace_Comment_Service();
        if ($cs->checkCommentDuplicate($item, $name, $comment))
            return self::DUPLICATE;

        // insert entry
        $values = array(
                    'ITEMTYPE'      => $item->getItemtypeID(),
                    'ITEMID'        => $item->getID(),
                    'LANGUAGE'      => $item->getLanguageID(),
                    'NAME'          => $name,
                    'EMAIL'         => $email,
                    'HOMEPAGE'      => $homepage,
                    'IP'            => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0'),
                    'COMMENT'       => $comment,
                    'TIMESTAMP'     => time(),
                    'ACTIVE'        => $activate,
                    'TYPE'          => $type,
                    'ANONYMOUS'     => $GLOBALS['_BIGACE']['SESSION']->isAnonymous());

        $sqlString = "INSERT INTO {DB_PREFIX}comments (`cid`, `itemtype`, `itemid`,
            `language`, `name`, `email`, `homepage`, `ip`, `comment`, `timestamp`,
            `activated`, `type`) VALUES ({CID}, {ITEMTYPE}, {ITEMID}, {LANGUAGE},
            {NAME}, {EMAIL}, {HOMEPAGE}, {IP}, {COMMENT}, {TIMESTAMP}, {ACTIVE}, {TYPE})";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

        if ($res->isError())
            return self::NOT_CREATED;

        if (!$activate) {
            $temp = Bigace_Config::get("comments", "email.recipient.moderator");
            if ($temp !== null && strlen(trim($temp)) > 0) {
                $this->_sendModeratorEmail(
                    $item, $temp,
                    getTranslation('comment.email.moderator.subject'),
                    getTranslation('comment.email.moderator.text'),
                    $values
                );
            }
            return self::AWAIT_MODERATION;
        } else {
            $temp = Bigace_Config::get("comments", "email.recipient.posting");
            if ($temp !== null && strlen(trim($temp)) > 0) {
                $this->_sendModeratorEmail(
                    $item, $temp,
                    getTranslation('comment.email.posting.subject'),
                    getTranslation('comment.email.posting.text'),
                    $values
                );
            }
        }

        return self::CREATED;
    }

    private function _sendModeratorEmail(Bigace_Item $item, $recipients, $subject, $content, $values)
    {
        $from = Bigace_Config::get("email", "from.address");
        $values['URL'] = LinkHelper::itemUrl($item);

        foreach ($values as $key => $val) {
            $content = str_replace("{".$key."}", $val, $content);
            $subject = str_replace("{".$key."}", $val, $subject);
        }

        $recipients = explode(",", trim($recipients));
        for ($i = 0; $i < count($recipients); $i++) {
            $recipient = $recipients[$i];

            if (strlen(trim($recipient)) > 3) {
                $email = new TextEmail();
                $email->setTo($recipient);
                $email->setFromEmail($from);
                $email->setSubject('[BIGACE] '. $subject);
                $email->setContent($content);
                $email->setCharacterSet('UTF-8');
                $didMail = $email->sendMail();
                if(!$didMail)
                    $GLOBALS['LOGGER']->logError('Could not send comment email to ' . $recipient);
            }
        }
    }

    /**
     * Deletes the Comment entry with the given ID.
     */
    function delete($id)
    {
        $sqlString = "DELETE FROM {DB_PREFIX}comments WHERE `id` = {ID} AND `cid` = {CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }

    /**
     * Marks the Entry as Spam and deletes the Comment entry with the given ID.
     */
    function deleteSpam($id)
    {
        $apiKey = Bigace_Config::get("comments", "akismet.api.key");
        if ($apiKey !== null && strlen(trim($apiKey)) > 0) {
            $service = new Bigace_Comment_Service();
            $comment = $service->getComment($id);

            require_once(BIGACE_3RDPARTY.'akismet.class.php');
            $comment = array(
                'author'    => $comment->getName(),
                'email'     => $comment->getEmail(),
                'website'   => $comment->getHomepage(),
                'body'      => $comment->getComment()
            );
            $akismet = new Akismet(BIGACE_HOME, trim($apiKey), $comment);
            if (!$akismet->errorsExist()) {
                $akismet->submitHam();
            } else {
                $errs = $akismet->getErrors();
                $err = "";
                foreach ($errs as $errName => $errMsg) {
                    $err .= " [".$errName."] " . $errMsg;
                }
                $GLOBALS['LOGGER']->logError('Failed submitting ham to Askimet Server: ' . $err);
            }
        }
        $this->increaseSpamCounter();
        return $this->delete($id);
    }

    public function increaseSpamCounter()
    {
        $sqlString = "UPDATE {DB_PREFIX}comment_spam_counter SET `counter` = `counter` + 1 WHERE `cid` = {CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array(), true);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Updates the Comment with the given ID.
     */
    public function update($id, $name, $comment, $email = '', $homepage = '')
    {
        $values = array(
                    'ID'            => $id,
                    'NAME'          => $name,
                    'EMAIL'         => $email,
                    'HOMEPAGE'      => $homepage,
                    'COMMENT'       => $comment
        );
        $sqlString = "UPDATE {DB_PREFIX}comments SET `name` = {NAME}, `email` = {EMAIL},
            `homepage` = {HOMEPAGE}, `comment` = {COMMENT} WHERE `cid` = {CID} AND `id` = {ID} ";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }

    /**
     * Deletes all Comments for the ItemID and Language.
     * If $language is null, all comments for this item will be deleted.
     */
    public function deleteAll($itemtype, $itemid, $language = null)
    {
        $values = array(
            'ITEMTYPE' => $itemtype,
            'ID' => $itemid
        );

        if (!is_null($language)) {
            $values['LANGUAGE'] = $language;
            $sql = "DELETE FROM {DB_PREFIX}comments WHERE itemtype = {ITEMTYPE}
              AND itemid = {ID} AND language = {LANGUAGE} AND cid = {CID}";
        } else {
            $sql = "DELETE FROM {DB_PREFIX}comments WHERE itemtype = {ITEMTYPE}
              AND itemid = {ID} AND cid = {CID}";
        }

        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }

    function activate($id)
    {
        $sqlString = "UPDATE {DB_PREFIX}comments SET `activated` = '1' WHERE id = {ID} AND cid = {CID}";
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }
}
