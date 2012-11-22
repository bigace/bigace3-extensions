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
 * @version $Id: function.link_item_languages.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.util.LinkHelper');
import('classes.language.ItemLanguageEnumeration');
import('classes.item.ItemService');

/**
 * Returns a list of links to switch the language of the current page.
 * 
 * Parameter:
 * - item       = REQUIRED the item to display links for
 * - assign     = name of the template variable
 * - spacer		= the spacer between the languages
 * - delimiter	= the delimiter between the passed locales
 * - css        = 
 * - hideActive = do not show active language link (default false)
 * - images		= if set to true images will displayed instead of the language names (default false)
 * - locale		= the locale to display the language names
 * - directory 	= where the images are taken from (optional)
 */
function smarty_function_link_item_languages($params, &$smarty)
{
	if(!isset($params['item'])) {
		trigger_error("link_item_languages: missing 'item' attribute");
		return;
	}
	
	$spacer		= (isset($params['spacer']) ? $params['spacer'] : ' ');
	$delimiter	= (isset($params['delimiter']) ? $params['delimiter'] : ',');
	$css 		= (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$hideActive = (isset($params['hideActive']) && ((bool)$params['hideActive']) === true) ? true : false;
	$images 	= (isset($params['images']) && ((bool)$params['images']) === true) ? true : false;
	$locForName = (isset($params['locale']) ? $params['locale'] : _ULC_);
	$dir 		= (isset($params['directory']) ? $params['directory'] : BIGACE_HOME.'system/images/' );
	$item 		= $params['item'];
	
	$ile = new ItemLanguageEnumeration($item->getItemtypeID(), $item->getID());
	$is = new ItemService($item->getItemtypeID());
	
	$html = '';
	for($i=0; $i < $ile->count(); $i++) 
	{
		$lang = $ile->next();
		if(!$hideActive || $lang->getLocale() != $item->getLanguageID()) 
		{
			$linkItem = $is->getClass($item->getID(), ITEM_LOAD_LIGHT, $lang->getLocale());
			$link = LinkHelper::getCMSLinkFromItem($linkItem);
			
			$html .= '<a'.$css.' href="'.LinkHelper::getUrlFromCMSLink($link).'" title="'.htmlspecialchars($linkItem->getName()).'">';
			if($images) {
				$html .= '<img src="'.$dir.$lang->getLocale().'.gif" alt="'.$lang->getName($locForName).'" border="0"/>';
			} else {
				$html .= $lang->getName($locForName);
			}
			$html .= '</a>';
			if($i+1 < $ile->count())
				$html .= $spacer;
		}
	}
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $html);
		return;
	}
	return $html;
}
