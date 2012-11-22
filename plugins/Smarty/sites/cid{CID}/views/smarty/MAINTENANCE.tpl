<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
{load_translation name="bigace"}
{load_translation name="maintenance"}
    <title>{translate key='error_title'}</title>
    <meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
    <meta NAME="generator" CONTENT="BIGACE">
    <meta NAME="robots" CONTENT="noindex, nofollow, noarchive">
    <link rel="stylesheet" href="{directory name="public"}system/css/error.css" type="text/css">
    <style type="text/css">
    {literal}
    body { background-color:#EFEFEF; color:#000000; }
    a {color:#000080;}
    a:hover {color:#0000FF;}
    {/literal}
    </style>
    <script type="text/javascript">
    <!-- {literal}
        function showLoginFormular()
        {
            switchVisibility('dynamicLoginForm');
            return false;
        }

        function switchVisibility(elementID)
        {
            if(document.getElementById(elementID).style.visibility != 'visible')
            {
                document.getElementById(elementID).style.visibility = 'visible';
                document.getElementById(elementID).style.display = 'block';
                return false;
            }
            else
            {
                document.getElementById(elementID).style.visibility = 'hidden';
                document.getElementById(elementID).style.display = 'none';
                return true;
            }
        }
    {/literal} // -->
    </script>
</head>
<body>
    <TABLE align="center" valign="middle" STYLE="margin-top:20px" width="630" CELLPADDING="0" CELLSPACING="0" border="0" bgcolor="#FFFFFF">
    <tr>
    <td width="22" valign="top" align="left"><img src="{directory name="public"}system/error/topleft.gif" border="0" width="22" height="20"></td>
    <td colspan="2" width="586">&nbsp;</td>
    <td width="22" valign="top" align="right"><img src="{directory name="public"}system/error/topright.gif" border="0" width="22" height="20"></td>
    </tr>

    <tr>
    <td width="22">&nbsp;</td>
    <td colspan="2" valign="top" align="left">
        <div>
            <div style="height:30px;border:0px;"></div>
            <strong>{translate key='error_title'}</strong><br /><br />
            <i>{$MESSAGE}</i>
            <div style="height:30px;border:0px;"></div>
        </div>
    </td>
    <td width="22">&nbsp;</td>
    </tr>

    <tr>
    <td width="22">&nbsp;</td>
    <td colspan="2" valign="top" align="left">

        <table border="0" width="100%">
            <tr>
                <td>
                    {if !$USER->isAnonymous()}
                    <a href="{link_logout}" class="logout">{translate key="logout"}</a><br/><br/>
                    {else}
                    <a href="{link_login}" class="login" onClick="return showLoginFormular();"><span id="loginText">{translate key="login"}</span></a>
                    {/if}
                </td>
                <td>
                </td>
            </tr>
        </table>
        <div id="dynamicLoginForm">
            <br/>
            <form action="{link_auth}" method="post">
                <table border="0">
                    <tr>
                        <td>{translate key="user"}:</td>
                        <td><input type="text" name="UID"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>{translate key="password"}:</td>
                        <td><input type="password" name="PW"></td>
                        <td><button type="submit">{translate key="login"}</button></td>
                    </tr>
                </table>
            </form>
        </div>
    </td>
    <td width="22">&nbsp;</td>
    </tr>

    <tr>
    <td width="22" valign="top" align="left"><img src="{directory name="public"}system/error/bottomleft.gif" border="0" width="22" height="20"></td>
    <td colspan="2">&nbsp;</td>
    <td width="22" valign="top" align="right"><img src="{directory name="public"}system/error/bottomright.gif" border="0" width="22" height="20"></td>
    </tr>

    <tr>
    <td width="22" bgcolor="#EFEFEF">&nbsp;</td>
    <td width="608" colspan="3" bgcolor="#EFEFEF" valign="top" align="left">
    &nbsp;<br />
    <br />
    </td>
    </tr>
    </table>
</body>
</html>
    
