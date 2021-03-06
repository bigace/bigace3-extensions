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
 * @version $Id: function.array_shift.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Same like array_unshift.
 */
function smarty_function_array_shift($params, &$smarty)
{
	if (!isset($params['assign'])) {
		trigger_error("array_unsihft: missing 'assign' attribute");
		return;
	}
	if (!isset($params['from'])) {
		trigger_error("array_unsihft: missing 'from' attribute");
		return;
	}
	$smarty->assign($params['assign'], array_shift(&$params['from']));
}
