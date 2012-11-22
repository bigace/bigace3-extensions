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
 * @version $Id: function.metatags.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Metatags dumps all the tags you get from the menu-page administration.
 *
 * Parameter:
 * item         = MENU    - required, the item to show the metatags for
 * assign       = STRING  - name of tpl variable to assign the javascript code to
 * prefix       = for html formatting
 * author       = defines author meta tag if set, otherwise will be skipped
 */
function smarty_function_metatags($params, &$smarty)
{
    if (!isset($params['item'])) {
        trigger_error("metatags: missing 'item' attribute");
        return;
    }

    $badge = '';
    $item  = $params['item'];
    $vhm   = new Bigace_Zend_View_Helper_Metatags();
    $view  = $smarty->getTemplateVars('VIEW');

    if ($view !== null && $view instanceof Zend_View_Interface) {
        $badge .= $view->headStyle();
        $badge .= $view->headLink();
        $badge .= $view->headMeta();
        $vhm->setView($view);
    }

    $badge .= $vhm->metatags($item, $params);

    if ($view !== null && $view instanceof Zend_View_Interface) {
        $badge .= $view->headScript();
    }

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $badge);
        return;
    }

    return $badge;
}