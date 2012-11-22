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
 * @version $Id: function.configuration.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Loads a configuration values.
 *
 * Parameter:
 * - cache
 * - package
 * - key
 * - default
 * - assign
 */
function smarty_function_configuration($params, &$smarty)
{
	if (!isset($params['cache'])) {
		if (!isset($params['package']) && !isset($params['key'])) {
			trigger_error("configuration: missing 'package' and 'key' or 'cache' attribute");
			return;
		}
	}

	if (isset($params['cache'])) {
		Bigace_Config::preload($params['cache']);
	}

	if (isset($params['package']) && isset($params['key'])) {
		$default = (isset($params['default']) ? $params['default'] : null);
		$value = Bigace_Config::get($params['package'], $params['key'], $default);
		if (isset($params['assign'])) {
			$smarty->assign($params['assign'], $value);
			return;
		}
		return $value;
	}
}
