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
 * @version $Id: function.comments_latest.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Purpose:  Fetches an array of the latest Comments
 *
 * Parameter:
 * - assign
 * - order (default: ASC - possible ASC/DESC)
 * - start (default: 0 - possible int)
 * - end (default: 10 - possible int)
 * - itemtype
 * - itemid
 * - language
 */
function smarty_function_comments_latest($params, &$smarty)
{
	if (!isset($params['assign'])) {
		trigger_error("comments_latest: missing 'assign' attribute");
		return;
	}

	$end = (isset($params['end']) ? $params['end'] : (isset($params['amount']) ? $params['amount'] : 10));

	$item = null;
	if(isset($params['item']))
	   $item = $params['item'];

    $service = new Bigace_Comment_Service();
    $comments = $service->getLatestComments($item, $end);

    $smarty->assign($params['assign'], $comments);
}
