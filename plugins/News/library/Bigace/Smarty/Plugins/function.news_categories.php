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
 * @version $Id: function.news_categories.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.category.CategoryTreeWalker');

/**
 * Purpose:  Returns all available News categories
 *
 * Parameter:
 * - assign (optional)
 */
function smarty_function_news_categories($params, &$smarty)
{
	$entries = array();

	$catWalk = new CategoryTreeWalker( Bigace_Config::get("news", "category.id") );

	for($i=0; $i < $catWalk->count(); $i++)
		$entries[] = $catWalk->next();

	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $entries);
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
	$amount = count($entries);

	for ($i = 0; $i < $amount; $i++) {
		$tempCat = $entries[$i];
//		$html .= $prefix . '<a href=""'.$css.' title="'.$tempCat->getName().'">'.$tempCat->getName().'</a>'
		$html .= $prefix . $tempCat->getName()
		       . (($i == ($amount-1)) ? $last : $after) . "\n";
	}

	return $html;
}

