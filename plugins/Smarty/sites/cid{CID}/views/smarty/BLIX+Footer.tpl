<div id="footer">
    <p><strong>&copy; Copyright {$smarty.now|date_format:"%Y"} {configuration package="community" key="copyright.holder" default="BIGACE CMS"}. All rights reserved.</strong><br />
    Powered by {bigace_version link=true full=true}. 
{if $USER->isAnonymous()}
	<a rel="nofollow" target="_self" href="{link_login item=$MENU}">{translate key="login"}</a>
{else}
    <a target="_blank" href="{link_admin}">{translate key="admin"}</a> | <a target="_self" href="{link_logout item=$MENU}">{translate key="logout"}</a>
{/if}
</p>
</div>

</div>
</body>
</html>
            
