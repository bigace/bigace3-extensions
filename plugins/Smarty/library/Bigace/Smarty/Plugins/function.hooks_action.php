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
 * @version $Id: function.hooks_action.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Activates an action hook.
 *
 * Parameter:
 * - name = the name of the action hook to call
 * - args = optional argument to pass to the action
 */
function smarty_function_hooks_action($params, &$smarty)
{
	if(!isset($params['name'])) {
		trigger_error("hooks_action: missing 'name' attribute");
		return;
	}

	if(count($params) > 1) {
	    $args = array();
	    foreach($params AS $k => $v) {
	        if($k != 'name')
	            $args[] = $v;
	    }

    	Bigace_Hooks::do_action($params['name'], $args);
    	return;
	}

	Bigace_Hooks::do_action($params['name'], (isset($params['args']) ? $params['args'] : ''));
}
