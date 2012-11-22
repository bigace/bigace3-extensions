<head>
    {$VIEW->adminBar()->includeJQuery(true)->enable()}
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="{$LAYOUT}style.css" type="text/css" media="screen, projection" >
    <link rel="stylesheet" href="{$LAYOUT}bigace_extension.css" type="text/css" media="screen, projection" >
	<link rel="shortcut icon" type="image/x-icon" href="{directory name="public"}system/images/favicon.ico" />
	{metatags item=$MENU}
    <script type="text/javascript">
    var xTerm1 = '{translate key="empty_searchterm"}';
    function doQuickSearch()
    {
    	if(document.getElementById('s').value.length == 0) {
            alert(xTerm1);
			return false;
        }
		return true;
    }
    </script>
    {hooks_action name="smarty_tpl_header" item=$MENU}
</head>
<body>
    {$VIEW->adminBar()}
	<div id="container">
	
	<div id="header"> 
	    <h1><a href="{link item=$topLevel}">{sitename|htmlspecialchars}</a></h1>
	    <div class="languages">{switch_language languages="de,en" hideActive="true"}</div>
	</div>
		
	<div id="navigation">
		<form action="{link_search id=$MENU->getID()}" method="post" onSubmit="return doQuickSearch();">
		    <fieldset>
		        <input type="text" name="search" value="" id="s" />
		        <input type="hidden" name="language" value="{$MENU->getLanguageID()}" />
		        <input type="submit" value="{translate key="search_button"}" id="searchbutton" name="searchbutton" />
		    </fieldset>
		</form>
		
	    <ul>
			{configuration package="blix.design" key="show.home.in.topmenu" default=true assign="homeInMenu"}
			{if $homeInMenu}
			<li><a href="{link item="$topLevel"}"{if $MENU->getID() == $topLevel->getID()} class="selected"{/if}>{$topLevel->getName()}</a></li>
			{/if}
			{navigation id="-1" prefix="<li>" suffix="</li>" selected="selected" select=$MENU->getID()}
	    </ul>
	</div>