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
 * @version $Id: function.portlet.php 175 2011-03-25 14:27:34Z kevin $
 * @package bigace.smarty
 * @subpackage function
 */

/**
 * Loads a single portlet.
 * 
 * Parameter: 
 * - assign = the variable name to bind the portlet to
 * - name 	= the Name of the Portlet Class (e.g. ToolPortlet)
 * - list 	= if you previously fetch portlets via {portlets assign="foo"} and you set {portlet list=$foo} the loaded portlet
              will be appended to this list
 * - params = a comma separated list of key=value pairs to set as portlet parameter (e.g. css=test,title=My Title) 
 */
function smarty_function_portlet($params, &$smarty)
{
	if(!isset($params['name'])) {
		trigger_error("portlet: attribute 'name' must be set to define the portlet to be loaded");
		return;
	}

	if(!isset($params['assign'])) {
		trigger_error("portlet: attribute 'assign' must be set");
		return;
	}
	
	$name = trim($params['name']);

    $fullClass = $name;
    if(!class_exists($fullClass))  {
        $fullClass = 'Bigace_Widget_Impl_'.$className;
    }
    
    if(class_exists($fullClass)) 
    {
        $portlet = new $fullClass();
        if (is_subclass_of($portlet, 'Bigace_Widget'))
        {
        	// FIXME
            // $portlet->setItem($item);
            if(isset($params['params']) && strlen(trim($params['params'])) > 0)
            {
                $portletParams = explode(',', $params['params']);
                foreach($portletParams as $pair) {
                    $pp = explode('=', $pair);
                    if(count($pp) == 2)
                        $portlet->setParameter($pp[0], $pp[1]);
                }
            }
            
            if(isset($params['list']))
                array_push($params['list'], $portlet);
                
            if(isset($params['list']))
                $smarty->assign_by_ref($params['assign'], $params['list']);
            else
                $smarty->assign_by_ref($params['assign'], $portlet);
        }
    }

    return;
}
