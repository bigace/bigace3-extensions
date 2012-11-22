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
 * @version $Id: function.load_translation.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Load a translation file either into the global or given namespace.
 *
 * Parameter:
 * - name
 * - locale
 * - namespace
 * - directory
 */
function smarty_function_load_translation($params, &$smarty)
{
	if (!isset($params['name'])) {
		trigger_error("load_translation: missing 'name' attribute");
		return;
	}

	$name = $params['name'];
	$locale = (isset($params['locale']) ? $params['locale'] : _ULC_);
	$directory = (isset($params['directory']) ? $params['directory'] : null);

	Bigace_Translate::loadGlobal($name, $locale, $directory);
}