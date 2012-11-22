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
 * @version $Id: function.comment_spam_counter.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Purpose:  Counts the amount of Spam for the Community
 *
 * Parameter:
 * - assign
 */
function smarty_function_comment_spam_counter($params, &$smarty)
{
    $cs = new Bigace_Comment_Service();
    $res = $cs->countSpam();

    if (isset($params['assign'])) {
    	$smarty->assign($params['assign'], $res);
    	return;
    }
    return $res;
}
