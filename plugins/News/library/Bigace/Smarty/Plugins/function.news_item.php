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
 * @version $Id: function.news_item.php 176 2011-03-25 14:56:37Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Purpose:  Load one News item and assign it to a Smarty variable
 *
 * Parameter:
 * - id
 * - assign
 * - language (optional)
 */
function smarty_function_news_item($params, &$smarty)
{
	if (!isset($params['id'])) {
		trigger_error("news_item: missing 'id' attribute");
		return;
	}
	if (!isset($params['assign'])) {
		trigger_error("news_item: missing 'assign' attribute");
		return;
	}
	$lang = (isset($params['language']) ? $params['language'] : Bigace_Config::get("news", "default.language"));

	$smarty->assign($params['assign'], new Bigace_News_Item($params['id'], $lang));

	return;
}
