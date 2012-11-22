<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id: function.link_admin.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.util.links.AdministrationLink');
import('classes.util.LinkHelper');

/**
 * Creates an URL for an Item or ID.
 * 
 * Parameter:
 * - id
 * - language
 * - params
 */
function smarty_function_link_admin($params, &$smarty)
{
    $link = new AdministrationLink();
    if(isset($params['id']))
    	$link->setItemID($params['id']);
    if(isset($params['language']))
    	$link->setLanguageID($params['language']);
	else if(defined('ADMIN_LANGUAGE'))
    	$link->setLanguageID(ADMIN_LANGUAGE);
	if(isset($params['params'])) {
		if(is_array($params['params']))
	    	return LinkHelper::getUrlFromCMSLink($link, $params['params']);
		$urlParam = array();
		$t1 = explode("&", $params['params']);
		foreach($t1 AS $pair) {
			$t2 = explode("=", $pair);
			if(count($t2) == 2)
				$urlParam[$t2[0]] = $t2[1];
		}
    	return LinkHelper::getUrlFromCMSLink($link, $urlParam);
	}
    return LinkHelper::getUrlFromCMSLink($link);
}
