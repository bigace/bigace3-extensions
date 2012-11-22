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
 * @version $Id: function.comments.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Fetches an array of Comments for an Item.
 * Is also capable to create new Comments.
 *
 * - assign
 * - preview
 * - item
 *   OR
 * - id
 * - language
 * - itemtype (default: _BIGACE_ITEM_MENU)
 */
function smarty_function_comments($params, &$smarty)
{
	if (!isset($params['assign'])) {
		trigger_error("comments: missing 'assign' attribute");
		return;
	}
	if (!isset($params['preview'])) {
		trigger_error("comments: missing 'preview' attribute");
		return;
	}
	if (!isset($params['item'])) {
        trigger_error("comments: missing 'item' attribute");
        return;
    }

    $itemtype = $params['item']->getItemTypeID();
	$id = $params['item']->getID();
	$language = $params['item']->getLanguageID();

	// empty preview
	$preview = array(
		'name' 		=> '',
		'email'		=> '',
		'homepage'	=> '',
		'comment'	=> ''
	);

	// FIXME captcha
	$captcha = null;
	if (Bigace_Config::get("comments", "use.captcha", false)) {
		$captcha = Bigace_Config::get("system", "captcha", null);
		if ($captcha == null) {
			trigger_error("comments: wrong configuration 'system/captcha'");
		}
	}

	$smarty->assign($params['preview'], $preview);

	if (!is_null($captcha)) {
		$smarty->assign("captcha", $captcha->get());
	}

	$service = new Bigace_Comment_Service();
	$comments = $service->getComments($params['item']);

    $smarty->assign($params['assign'], $comments);
}
