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
 * @version $Id: function.hooks_has_action.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Checks is an Action Hook has been registered.
 * 
 * Parameter:
 * - name = the name of the action to check
 * - assign = if set, the resultvalue will be assigned instead of being returned 
 */
function smarty_function_hooks_has_action($params, &$smarty)
{
	if(!isset($params['name'])) {
		trigger_error("hooks_has_action: missing 'name' attribute");
		return;
	}
	
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], Bigace_Hooks::has_action($params['name']));
		return;
	}
	
	return Bigace_Hooks::has_action($params['name']);
}
