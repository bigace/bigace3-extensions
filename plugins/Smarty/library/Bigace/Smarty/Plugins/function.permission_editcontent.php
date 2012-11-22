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
 * @version $Id: function.permission_editcontent.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Checks if a User is allowed to edit a pages content
 * with one of the html editors.
 * 
 * Parameter:
 * - id
 * - assign
 */
function smarty_function_permission_editcontent($params, &$smarty)
{
	$id = (isset($params['id']) ? $params['id'] : $GLOBALS['_BIGACE']['PARSER']->getItemID());	

	$check = new Bigace_Acl_Check_EditContent($id);

	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $check->isAllowed());
		return;
	}	

	return $check->isAllowed();
}
