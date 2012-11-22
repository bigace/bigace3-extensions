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
 * @version $Id: function.news_category.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.category.ItemCategoryEnumeration');

/**
 * Purpose:  Returns the News categories, this Item is linked to.
 *
 * Parameter:
 * - id
 * - item
 * - assign
 * - start
 * - css
 * - prefix
 * - suffix
 * - last
 */
function smarty_function_news_category($params, &$smarty)
{
	if (!isset($params['id']) && !isset($params['item'])) {
		trigger_error("news_category: missing 'id' AND 'item' attribute");
		return;
	}

	// one of ID and item must be set!
	$id = isset($params['id']) ? $params['id'] : $params['item']->getID();
	$rootCat = Bigace_Config::get("news", "category.id");

	$ice = new ItemCategoryEnumeration(_BIGACE_ITEM_MENU, $id);

	$cats = array();

	while ($ice->hasNext()) {
		$t = $ice->next();
		if($t->getParentID() == $rootCat)
			$cats[] = $t;
	}

	// return the array with news categories
	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $cats);
		return;
	}

	// otherwise we create the required html
	$html = (isset($params['start']) ? $params['start'] : '');
	$css = (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$prefix = (isset($params['prefix']) ? $params['prefix'] : '');
	// html to render behind each link
	$after = (isset($params['suffix']) ? $params['suffix'] : ', ');
	// last is empty, becaue we redner a comma separated list by default
	$last = (isset($params['last']) ? $params['last'] : '');
	$amount = count($cats);

	for ($i = 0; $i < $amount; $i++) {
		$tempCat = $cats[$i];
//		$html .= $prefix . '<a href=""'.$css.' title="'.$tempCat->getName().'">'.$tempCat->getName().'</a>'
		$html .= $prefix .$tempCat->getName();
		if($i < ($amount-1))
			$html .= $after;
		else
 			$html .= $last;
	}

	return $html;
}
