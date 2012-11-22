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
 * @version $Id: function.switch_language.php 175 2011-03-25 14:27:34Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Additional Parameter:
 *
 * - delimiter	= the delimiter between the passed locales
 * - alttexts	= delimiter separated list of alt texts for the images (order of text must be order as given in languages parameter)
 *
 * @see Bigace_Zend_View_Helper_LanguageChooser
 */
function smarty_function_switch_language($params, &$smarty)
{
	if(!isset($params['languages'])) {
		trigger_error("switch_language: missing 'languages' attribute");
		return;
	}

	$delimiter	= (isset($params['delimiter']) ? $params['delimiter'] : ',');
	$languages 	= explode($delimiter, $params['languages']);
    $alttexts	= (isset($params['alttexts']) ? explode($delimiter, $params['alttexts']) : array());
    
    $params['alttexts'] = $alttexts;
    
    $lc = new Bigace_Zend_View_Helper_LanguageChooser();
	
	return $lc->languageChooser($languages, $params);
}
