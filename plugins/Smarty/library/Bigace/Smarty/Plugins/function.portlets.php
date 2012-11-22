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
 * @version $Id: function.portlets.php 177 2011-03-28 08:15:39Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Loads portlets. If parameter 'assign' is not set, it will return the portlets.
 *
 * Required parameter:
 * - item   = the item to load the portlets for
 *
 * Parameter:
 * - assign = the variable name that is used to bind Portlets in Smarty Context
 * - id     = if not "item" attribute is set "id" and "lang" are required. ID of the item to load portlets for.
 * - lang   = if not "item" attribute is set "id" and "lang" are required. Language to fetch portlets for (default: current language).
 * - name   = the Name of the Portlet Column (comma separated list, default: loads all Portlets for Item/Language)
 */
function smarty_function_portlets($params, &$smarty)
{
    static $portletCache;

    if (!isset($params['item'])) {
        if (!isset($params['id']) && !isset($params['lang'])) {
            trigger_error("portlets: 'item' attribute not set");
            return;
        }
    }
    $id = isset($params['item']) ? $params['item']->getID() : $params['id'];
    $lang = isset($params['item']) ? $params['item']->getLanguageID() : $params['lang'];
    $name = isset($params['name']) ? $params['name'] : null;
    $item = isset($params['item']) ? $params['item'] : null;

    if ($item === null) {
        $item = Bigace_Item_Basic::get(_BIGACE_ITEM_MENU, $id, $lang);
    }

    if(is_null($portletCache))
        $portletCache = array();

    $cacheKey = $id . '_' . $lang . md5($name);

    if (!isset($portletCache[$cacheKey])) {
        $vhp = new Bigace_Zend_View_Helper_Widgets();

        $view = $smarty->getTemplateVars('VIEW');
        if ($view !== null && $view instanceof Zend_View_Interface) {
            $vhp->setView($view);
        }

        $portlets = $vhp->widgets($item, $name);
        $portletCache[$cacheKey] = $portlets;
    }

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $portletCache[$cacheKey]);
        return;
    }

    return $portletCache[$cacheKey];
}
