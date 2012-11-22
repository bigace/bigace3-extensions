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
 * @version $Id: modifier.text_input.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Modifier
 */

/**
 * Prepares any value to be edited in an <input type="text">.    
 */
function smarty_modifier_text_input($str)
{
	$str = str_replace('"', '&quot;', $str);
    $str = str_replace("'", '&#039;', $str);
    return stripslashes($str);
}
