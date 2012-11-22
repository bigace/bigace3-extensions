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
 * @version $Id: function.most_visited.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Fetches an array of Menu Items from underneath a specified parent.
 *
 * Parameter:
 * - itemtype
 * - id
 * - language
 * - assign
 * - counter
 * - from
 * - to
 */
function smarty_function_most_visited($params, &$smarty)
{
	if (!isset($params['assign'])) {
		trigger_error("most_visited: missing 'assign' attribute");
		return;
	}
	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU);
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());
	$from = (isset($params['from']) ? $params['from'] : 0);
	$to = (isset($params['to']) ? $params['to'] : (isset($params['amount']) ? $params['amount'] : 5));

	$ir = new Bigace_Item_Request($itemtype);
	if (isset($params['id'])) {
		$ir->setID(isset($params['id']));
	}

	$ir->setLimit($from,$to);
	$ir->setLanguageID($lang);
	$menu_info = Bigace_Item_Requests::getMostVisited($ir);

	$items = array();

	if (isset($params['counter'])) {
		$smarty->assign($params['counter'], $menu_info->count());
	}

    for ($i=0; $i < $menu_info->count(); $i++) {
		$items[] = $menu_info->next();
    }

	$smarty->assign($params['assign'], $items);
}
