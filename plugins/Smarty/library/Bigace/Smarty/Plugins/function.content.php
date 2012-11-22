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
 * @version $Id: function.content.php 178 2011-03-28 08:16:21Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Load additional content for a menu.
 *
 * Parameter:
 * item         = the menu to fetch the content for (or 'id' and 'language')
 * id           =
 * language     =
 * target       = target media, might be rendered different in the future
 * assign       = name of the template variable to be assigned to
 * name         =
 */
function smarty_function_content($params, &$smarty)
{

    $view = $smarty->getTemplateVars('VIEW');
    if ($view === null || !($view instanceof Zend_View_Interface)) {
        trigger_error("content: missing 'VIEW' attribute");
        return;
    }

    $cnt = new Bigace_Zend_View_Helper_Content();
    $cnt->setView($view);
    if (isset($params['name'])) {
        $cnt->withName($params['name']);
    }
    return (string)$cnt;
}
