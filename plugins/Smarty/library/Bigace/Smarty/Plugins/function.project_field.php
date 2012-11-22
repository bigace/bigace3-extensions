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
 * @version $Id: function.project_field.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Smarty plugin to fetch project item values.
 *
 * Parameter:
 * - assign   = the variable name that is used to bind result in smarty context
 * - item 	= the item to fetch project values for
 * - type		= "text" or "num" (default: "text")
 * - name 	= the name of the project value (if not set all parameter are returned)
 * - default  = value returned if found project field with name "name" is not found (default: null). not evaluated if name is missing.
 */
function smarty_function_project_field($params, &$smarty)
{
	if(!isset($params['assign'])) {
		trigger_error("project_field: missing 'assign' attribute");
		return;
	}
	if(!isset($params['item'])) {
		trigger_error("project_field: missing 'item' attribute");
		return;
	}

	$item = $params['item'];
	$type = isset($params['type']) ? $params['type'] : "text";
	$name = isset($params['name']) ? $params['name'] : "";
	$default = isset($params['default']) ? $params['default'] : null;

    if(empty( $name ))
    {
	    if($type == "text") {
			$pt = new Bigace_Item_Project_Text();
			$result = $pt->getAll($item);
	    } else {
			$pn = new Bigace_Item_Project_Numeric();
			$result = $pt->getAll($item);
    	}
    }
	else
	{
	    if($type == "text") {
			$pt = new Bigace_Item_Project_Text();
			$result = $pt->get($item, $name);
			if ($result === null) {
			    $result = $default;
			}
	    } else {
			$pn = new Bigace_Item_Project_Numeric();
			$result = $pn->get($item, $name);
			if ($result === null) {
			    $result = $default;
			}
	    }
	}

	$smarty->assign($params['assign'], $result);
	return;
}
