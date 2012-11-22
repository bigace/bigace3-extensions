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
 * @version $Id: function.load_item.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.item.ItemService');

/**
 * Load an Item and assign it to a Smarty variable.
 * 
 * Parameter:
 * - id
 * - assign
 * - itemtype
 * - language
 */
function smarty_function_load_item($params, &$smarty)
{
	if(!isset($params['id'])) {
		trigger_error("load_item: missing 'id' attribute");
		return;
	}	

	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU); 
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());	
	
	$service = new ItemService($itemtype);
	$item = $service->getItem($params['id'], ITEM_LOAD_FULL, $lang);
	
	if(isset($params['assign'])) {
    	$smarty->assign($params['assign'], $item);
    	return;
    }
    
	return $item;
}
