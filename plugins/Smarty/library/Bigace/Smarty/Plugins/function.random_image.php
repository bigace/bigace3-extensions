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
 * @version $Id: function.random_image.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Returns the URL to a random image from one folder.
 *
 * Parameter:
 * - dir		= directory to look for images
 * - extensions = allowed file extensions to use
 * - assign     = 
 */
function smarty_function_random_image($params, &$smarty)
{
	if(!isset($params['dir'])) {
		trigger_error("random_image: missing 'dir' parameter");
		return;
	}
	
	$extensions = isset($params['extensions']) ? $params['extensions'] : "gif,png,jpg,jpeg";
	$image = "";
	$extensions = strtolower($extensions);
	
	$allFiles = array();
	if(is_dir($params['dir'])) 
	{
		$handle = opendir($params['dir']);
		while (false !== ($file = readdir($handle))) 
		{
			if($file != "." && $file != "..") {
				foreach(explode($extensions) AS $ext) {
					if(strripos(strtolower($file), $ext) !== false)
						$allFiles[] = $file;
				}
			}
		}
	}
	
	$image = array_rand($allFiles);
	    
	if(isset($params['assign'])) {
		$smarty->assign($params['assign'], $image);
		return;
	}
	
	return $image;
}
