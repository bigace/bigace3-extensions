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
 * @version $Id: function.last_edited_items.php 164 2011-03-25 09:39:42Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Fetch or display the latest edited items.
 *
 * Parameter:
 * - itemtype
 * - id
 * - language
 * - from
 * - to
 * - order
 * - assign
 * - pre
 * - post
 * - preDate
 * - postDate
 * - description
 * - date
 */
function smarty_function_last_edited_items($params, &$smarty)
{
	$itemtype = (isset($params['itemtype']) ? $params['itemtype'] : _BIGACE_ITEM_MENU);
	$id = (isset($params['id']) ? $params['id'] : null);
	$language = (isset($params['language']) ? $params['language'] : $GLOBALS['_BIGACE']['PARSER']->getLanguage());
	$from = (isset($params['from']) ? $params['from'] : 0);
	$to = (isset($params['to']) ? $params['to'] : (isset($params['amount']) ? $params['amount'] : 5));

	$ir = new Bigace_Item_Request($itemtype);
	$ir->setID($id);
	$ir->setLimit($from, $to);
	$ir->setLanguageID($language);
	if (isset($params['order'])) {
		$ir->setOrder($params['order']);
	}

	$temp = Bigace_Item_Requests::getLastEditedItems($ir);

	$items = array();
	for($i=0; $i < $temp->count(); $i++)
		$items[] = $temp->next();

	if (isset($params['assign'])) {
		$smarty->assign($params['assign'], $items);
		return;
	}

	$pre = (isset($params['pre']) ? $params['pre'] : '<li>');
	$post = (isset($params['post']) ? $params['post'] : '</li>');

	$preDate = (isset($params['preDate']) ? $params['preDate'] : '<i>');
	$postDate = (isset($params['postDate']) ? $params['postDate'] : '</i>');

	// -1 = hide description, 0 = full length, every other value = allowed length
	$desc = (isset($params['description']) ? $params['description'] : -1);
	$date = (isset($params['date']) ? $params['date'] : null);

	$html = '';

	foreach ($items as $lastEdited) {
        $html .= $pre . '<a href="' . LinkHelper::itemUrl($lastEdited) .
            '">' . $lastEdited->getName() . '</a><br/>';
        if ($desc != -1 && strlen($lastEdited->getDescription()) > 0) {
        	if ($desc == 0) {
            	$html .= $lastEdited->getDescription();
        	} else {
            	$html .= substr($lastEdited->getDescription(), 0, $desc);
                if (strlen($lastEdited->getDescription()) > ($desc+3))
                    $html .= '...';
            }
            $html .= '<br />';
        }
        if($date != null)
        	$html .= $preDate . date($date, $lastEdited->getLastDate()) . $postDate;
        $html .= $post;
    }
	return $html;
}
