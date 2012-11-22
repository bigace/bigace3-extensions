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
 * @version $Id: function.translate.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Translate a given key from the global (or a given) namespace.
 *
 * Requires vsprintf (PHP >= 4.10)
 *
 * Parameter:
 * - key		=
 * - default	=
 * - namespace	=
 * - sprintf	=
 * - delimiter	=
 * - assign		=
 */
function smarty_function_translate($params, &$smarty)
{
	if (!isset($params['key'])) {
		trigger_error("translate: missing 'key' attribute");
		return;
	}

	$default = (isset($params['default']) ? $params['default'] : null);

		$t =  Bigace_Translate::getGlobal()->_($params['key']);

	if ($t === null)
	   $t = $default;

	if (isset($params['sprintf'])) {
		$delim = (isset($params['delimiter']) ? $params['delimiter'] : ',');
		$values = explode($delim, $params['sprintf']);
		$t = vsprintf($t, $values);
	}

	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $t);
		return;
	}

	return $t;
}
