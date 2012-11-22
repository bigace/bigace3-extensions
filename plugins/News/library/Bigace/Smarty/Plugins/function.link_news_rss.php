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
 * @version $Id: function.link_news_rss.php 193 2011-04-11 09:30:02Z kevin $
 * @package Bigace_Smarty
 * @subpackage Function
 */

/**
 * Returns the URL to the News RSS feed.
 */
function smarty_function_link_news_rss($params, &$smarty)
{
    $feedUrl = Bigace_Config::get('news', 'feeds.custom.url', '');

    // a basic url needs more than 6 character, but if the user enters at least a
    // protocol, he gets what he wants ;)
    if (strlen(trim($feedUrl)) > 6) {
        return LinkHelper::url($feedUrl);
    }

    return LinkHelper::url("news/feed/index/type/rss");
}
