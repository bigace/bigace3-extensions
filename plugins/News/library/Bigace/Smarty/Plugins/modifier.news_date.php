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
 * @version $Id: modifier.news_date.php 169 2011-03-25 12:14:28Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Smarty modifier
 * Purpose:  Encodes the timestamp as RFC 2822 compatible date    
 */
function smarty_modifier_news_date($str)
{
    return date('r', $str);
}
