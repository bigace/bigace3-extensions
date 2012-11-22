<?php
/*
 * Prints or assigns the *NEW* Javascript Code for Google Analytics.
 *
 * Parameter:
 * 'id' 		= STRING  - googles analytics id (UA-xxxxxx-x)
 * 'assign' 	= STRING  - name of tpl variable to assign the javascript code to
 * 'track_user' = BOOLEAN - whether logged in user should be tracked or not (default false)
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Swen Wendler, Kevin Papst
 * @copyright Copyright (C) Swen Wendler, Kevin Papst
 * @version $Id: function.google_analytics.php 4 2011-01-31 13:41:05Z kevin $
 */
function smarty_function_google_analytics($params, &$smarty)
{
    if (!isset($params['id'])) {
        trigger_error("google_analytics: missing 'id' attribute");
        return;
    }

    $loggedIn  = isset($params['track_user']) ? (bool) $params['track_user'] : false;
    $anonymous = Zend_Registry::get('BIGACE_SESSION')->isAnonymous();

    if ($loggedIn || $anonymous) {
	    $badge  = "<script type=\"text/javascript\">\n";
	    $badge .= "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
	    $badge .= "document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
	    $badge .= "</script>\n";
		$badge .= "<script type=\"text/javascript\">\n";
		$badge .= "try {\n";
	    $badge .= "var pageTracker = _gat._getTracker(\"".trim($params['id'])."\");\n";
	    $badge .= "pageTracker._trackPageview();\n";
	    $badge .= "} catch(err) {}</script>\n";

		if (isset($params['assign'])) {
			$smarty->assign($params['assign'], $badge);
			return;
		}

		return $badge;
    }

    return;
}