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
 * @version $Id: function.language.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Return languages.
 *
 * Parameter:
 * - name = (optional, default "session") possible values: "session", "default"
 * - assign = (optional)
 */
function smarty_function_language($params, &$smarty)
{
	$type = isset($params['type']) ? strtolower($params['type']) : "session";
	$ret = "";

	switch($type) {
		case 'default':
			$ret = Bigace_Config::get('community', 'default.language', 'en');
		case 'session':
		default:
			$ret = _ULC_;
			break;
	}

	if (isset($params['assign'])) {
	    $smarty->assign($params['assign'], $ret);
	    return;
	}

	return $ret;
}
