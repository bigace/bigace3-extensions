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
 * @version $Id: function.news.php 176 2011-03-25 14:56:37Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Purpose:  Fetches an array of plain News items
 *
 * Parameter:
 * - orderby
 * - order
 * - assign
 * - counter
 * - start / end
 * - category
 * - limit (null or 0 will be skipped)
 * - language (optional)
 */
function smarty_function_news($params, &$smarty)
{
	if (!isset($params['assign'])) {
		trigger_error("news: missing 'assign' attribute");
		return;
	}

	$itemtype = _BIGACE_ITEM_MENU;
	$id = Bigace_Config::get("news", "root.id");
	$lang = (isset($params['language']) ? $params['language'] : Bigace_Config::get("news", "default.language"));
	$order = (isset($params['order']) ? $params['order'] : 'DESC');
	$orderby = (isset($params['orderby']) ? $params['orderby'] : "date_2");

	$from = (isset($params['start']) ? $params['start'] : 0);
	$to = (isset($params['end']) ? $params['end'] : (isset($params['limit']) ? $params['limit'] : null));

	if ($id == null) {
		trigger_error("news: Configuration news/root.id not set");
		return;
	}

	$ir = new Bigace_Item_Request($itemtype);
	$ir->setID($id);
	$ir->setLanguageID($lang);
	$ir->setReturnType("Bigace_News_Item");
	$ir->setOrderBy($orderby);
	$ir->setOrder($order);
	if (isset($params['hidden']) && $params['hidden'] === true) {
		$ir->addFlagToInclude(Bigace_Item_Request::HIDDEN);
	}

	if ($to != null && $to != 0) {
		$ir->setLimit($from, $to);
	}

	if (isset($params['category']) && $params['category'] != '') {
		if (strpos($params['category'], ",") === FALSE) {
			$ir->setCategory($params['category']);
		} else {
			$tmp = explode(",", $params['category']);
			foreach ($tmp AS $x) {
				$ir->setCategory($x);
			}
		}
	}

	$menuInfo = new Bigace_Item_Walker($ir);

	$items = array();

	if (isset($params['counter'])) {
		$smarty->assign($params['counter'], $menuInfo->count());
	}

    for ($i=0; $i < $menuInfo->count(); $i++) {
		$items[] = $menuInfo->next();
    }

	$smarty->assign($params['assign'], $items);
}
