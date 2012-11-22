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
 * @version $Id: function.breadcrumb.php 167 2011-03-25 09:42:16Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

import('classes.util.LinkHelper');
import('classes.right.RightService');
import('classes.menu.MenuService');

/**
 * Prints or fetches a Breadcrumb Navigation
 *
 * Parameter:
 * id =
 * language =
 * top =
 * css =
 * prefix =
 * suffix =
 * counter =
 * assign =
 * hidden =
 */
function smarty_function_breadcrumb($params, &$smarty)
{
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());
	$lang = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());
	$top = (isset($params['top']) ? $params['top'] : _BIGACE_TOP_PARENT);
	$css = (isset($params['css']) ? ' class="'.$params['css'].'"' : '');
	$showHidden = ((isset($params['hidden']) && strtolower($params['hidden']) == 'true') ? true : false);
	$pre = (isset($params['prefix']) ? $params['prefix'] : '');
	$post = (isset($params['suffix']) ? $params['suffix'] : '');

    $wayHomeInfo = array();
    $mService = new MenuService();

    while ($id != $top) {
	    $current = $mService->getMenu($id, $lang);
    	if (!$current->isHidden() || $showHidden) {
	        if (has_item_permission(_BIGACE_ITEM_MENU, $current->getID(), 'r')) {
				$link = LinkHelper::getCMSLinkFromItem($current);
	            array_push($wayHomeInfo, array($current->getName(), LinkHelper::getUrlFromCMSLink($link)));
	        }
    	}
        $id = $current->getParentID();
    }

	if (isset($params['counter'])) {
		$smarty->assign($params['counter'], count($wayHomeInfo));
	}

	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $wayHomeInfo);
		return;
	}

	$html = '';

	$wayHomeInfo = array_reverse($wayHomeInfo);

    for ($i=0; $i<count($wayHomeInfo); $i++) {
    	if($i!=0)
    		$html .= $pre;
		$html .= "<a href=\"".$wayHomeInfo[$i][1]."\"".$css.">".$wayHomeInfo[$i][0]."</a>";
		if($i < (count($wayHomeInfo)-1))
			$html .= $post."\n";
    }

    return $html;
}
