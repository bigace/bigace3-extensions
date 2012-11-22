<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$MENU->getLanguageID()}" lang="{$MENU->getLanguageID()}">
{load_translation name="blix"}
{load_translation name="bigace"}
{load_item id="-1" itemtype="1" assign="topLevel"}
{configuration cache="blix.design"}
{include file="BLIX+Header.tpl"}
<hr class="low" />

<div id="content">
  <div class="entry single">
	{content}
	<p class="info">
		<em class="date">{translate key="written_at"} {$MENU->getCreateDate()|date_format:"%d. %B %Y"} {translate key="written_by"} {user id=$MENU->getCreateByID()}</em>
	</p>
	
	{hooks_action name="smarty_tpl_footer" item=$MENU}
	
  </div>
</div>

<hr class="low" />

<div id="subcontent">

{portlets assign="blixPortlets" item=$MENU}
{foreach from=$blixPortlets item="myPortlet"}
   <h2><em>{$myPortlet->getTitle()}</em></h2>
   {$myPortlet->getHtml()}
{/foreach}

</div>

<hr class="low" />
{include file="BLIX+Footer.tpl"}