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
 * @version $Id: modifier.date_form.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Modifier
 */

/**
 * Smarty modifier for date formatting, by using the given format.
 * Format must be compatible with the PHP date() function.
 *
 * Default format is 'r': encode the timestamp as RFC 2822 compatible date
 */
function smarty_modifier_date_form($str, $format = 'r')
{
    return date($format, $str);
}

