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
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 * @version    $Id$
 */

/**
 * Formats a comment entry.
 *
 * - Link URLs automatcially
 * - Uses the "clean_url" and "comment_format" filters
 *
 * Some of the code in here is taken from the Wordpress files:
 * - /wp-includes/kses.php
 * - /wp-includes/formatting.php
 *
 * @category   Bigace
 * @package    Bigace_Zend
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2009-2010 Keleo (http://www.keleo.de)
 * @license    http://www.bigace.de/license.html     GNU Public License
 */
class Bigace_Zend_View_Helper_CommentFormat extends Zend_View_Helper_Abstract
{
    /**
     * @var string
     */
    private $comment = null;
    /**
     * @var boolean
     */
    private $nofollow = false;
    /**
     * @var array
     */
    private $protocols = array(
        'http', 'https', 'ftp', 'ftps', 'mailto',
        'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'
    );

    /**
     * @param string $comment
     * @return Bigace_Zend_View_Helper_CommentFormat
     */
    public function commentFormat($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param boolean $state
     * @return Bigace_Zend_View_Helper_CommentFormat
     */
    public function setNofollow($state)
    {
        $this->nofollow = (bool)$state;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNofollow()
    {
        return $this->nofollow;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $ret = ' ' . $this->comment;
        // using arrays here was found to be faster during testing
        $ret = preg_replace_callback(
            '#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
            array($this, 'getClickableUrl'),
            $ret
        );
        $ret = preg_replace_callback(
            '#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
            array($this, 'getClickableWebFtp'),
            $ret
        );
        $ret = preg_replace_callback(
            '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i',
            array($this, 'getClickableEmail'),
            $ret
        );
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);

        $ret = trim($ret);
        $ret = nl2br($ret);

        return Bigace_Hooks::apply_filters('comment_format', $ret);
    }

    // ===================================================================================
    // Helper functions
    // ===================================================================================

    /**
     * @param string $url
     * @param string $text
     * @return string
     */
    private function makeLinkDoNoFollow($url, $text)
    {
        $temp  = '<a href="'.$url.'"';
        if ($this->isNofollow()) {
            $temp .= ' rel="nofollow"';
        }
        $temp .= '>'.$text.'</a>';
        return $temp;
    }

    /**
     * @param string $string
     * @return string
     */
    private function decodeEntities($string)
    {
        $string = preg_replace_callback('/&#([0-9]+);/', function ($value) {
            if (is_array($value)) {
                return chr($value[1]);
            }

            return chr($value);
        }, $string);

        $string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', function ($value) {
            if (is_array($value)) {
                return chr(hexdec($value[1]));
            }
            return chr(hexdec($value));
        }, $string);

        return $string;
    }

    /**
     * Removes any NULL characters in $string.
     *
     * @param string $string
     * @return string
     */
    private function noNull($string)
    {
        $string = preg_replace('/\0+/', '', $string);
        $string = preg_replace('/(\\\\0)+/', '', $string);

        return $string;
    }

    // ===================================================================================
    // Callback functions
    // ===================================================================================

    public function getClickableUrl($matches)
    {
        $ret = '';
        $url = $matches[2];
        $url = $this->cleanUrl($url);
        if (empty($url)) {
            return $matches[0];
        }

        // removed trailing [.,;:] from URL
        if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
            $ret = substr($url, -1);
            $url = substr($url, 0, strlen($url)-1);
        }

        return $matches[1] . $this->makeLinkDoNoFollow($url, $url) . '</a>' . $ret;
    }

    public function getClickableWebFtp($matches)
    {
        $ret = '';
        $dest = $matches[2];
        $dest = 'http://' . $dest;
        $dest = $this->cleanUrl($dest);
        if (empty($dest)) {
            return $matches[0];
        }

        // removed trailing [,;:] from URL
        if (in_array(substr($dest, -1), array('.', ',', ';', ':')) === true) {
            $ret = substr($dest, -1);
            $dest = substr($dest, 0, strlen($dest)-1);
        }
        return $matches[1] . $this->makeLinkDoNoFollow($dest, $dest) . '</a>' . $ret;
    }

    public function getClickableEmail($matches)
    {
        $email = $matches[2] . '@' . $matches[3];
        return $matches[1] . '<a href="mailto:'.$email.'">'.$email.'</a>';
    }

    // ===================================================================================
    // All the magic comes below
    // ===================================================================================

    protected function cleanUrl($url)
    {
        $original = $url;

        if ('' == $url) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@()]|i', '', $url);
        $url = str_replace(array('%0d', '%0a'), '', $url);
        $url = str_replace(';//', '://', $url);

        /* If the URL doesn't appear to contain a scheme, we
         * presume it needs http:// appended (unless a relative
         * link starting with / or a php file).
        */
        if (strpos($url, ':') === false &&
            substr( $url, 0, 1 ) != '/' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
            $url = 'http://' . $url;
        }

        // Replace ampersands
        $url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);

        if ($this->fixBadProtocol($url, $this->protocols) != $url) {
            return '';
        }

        return Bigace_Hooks::apply_filters('clean_url', $url, $original);
    }

    protected function fixBadProtocol($string, $allowedProtocols)
    {
        $string = $this->noNull($string);
        $string = preg_replace('/\xad+/', '', $string); # deals with Opera "feature"
        $string2 = $string.'a';

        while ($string != $string2) {
            $string2 = $string;
            $string  = $this->fixBadProtocolOnce($string, $allowedProtocols);
        }

        return $string;
    }

    protected function fixBadProtocolOnce($string, $allowedProtocols)
    {
        $string2 = preg_split('/:|&#58;|&#x3a;/i', $string, 2);
        if ( isset($string2[1]) && !preg_match('%/\?%', $string2[0]) ) {
            $string = $this->fixBadProtocolOnce2($string2[0], $allowedProtocols) . trim($string2[1]);
        } else {
            $string = preg_replace_callback(
                '/^((&[^;]*;|[\sA-Za-z0-9])*)'.'(:|&#58;|&#[Xx]3[Aa];)\s*/',
                array($this, 'fixBadProtocolOnce3'),
                $string
            );
        }

        return $string;
    }

    protected function fixBadProtocolOnce2($string, $allowedProtocols)
    {
        $string2 = $this->decodeEntities($string);
        $string2 = preg_replace('/\s/', '', $string2);
        $string2 = $this->noNull($string2);
        $string2 = preg_replace('/\xad+/', '', $string2);
        # deals with Opera "feature"
        $string2 = strtolower($string2);

        $allowed = false;
        foreach ($allowedProtocols as $one_protocol)
            if (strtolower($one_protocol) == $string2) {
                $allowed = true;
                break;
            }

        if ($allowed) {
            return "$string2:";
        }

        return '';
    }

    /**
     * Needs to be public, because of the callback in 'fixBadProtocolOnce'.
     */
    public function fixBadProtocolOnce3($matches)
    {
        return $this->fixBadProtocolOnce2($matches[1], $this->protocols);
    }

}
