<?php
ini_set('magic_quotes_gpc', 1);

if (!get_magic_quotes_gpc()) {
    die("Insecure - magic quotes not enabled");
}

require("pureuserclass.php");
$a = new pureuseradmin();

switch ($_POST["action"]) {
    case "edit_user" :
        edit_user($_POST["username"]);
        break;
    case "save_user" :
        $a->save_user($_POST["userinfo"]);
        gen_list();
        break;
    case "delete_user" :
        $a->delete_user($_POST["userinfo"]);
        gen_list();
        break;
    case "search" :
        gen_list($_REQUEST["searchstring"], $_REQUEST["start"]);
        break;
    default :
        welcome();
        break;
}

function html_header($title)
{
    global $a;
    ?>
    <?= "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n" ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <title>PureUserAdmin - <?= $title ?></title>
        <style type="text/css">
            .copyright {
                bottom: 0px;
                right: 0px;
                height: 15px;
                border-top: 3px solid #6699CC;
                border-bottom: 3px solid #6699CC;
                background-color: #336699;
                color: #E4E9EB;
            }

            html, body, form {
                height: 100%;
                width: 100%;
                margin: 0px 0px 0px 0px;
            }

            td {
                background-color: #FFFFFF;
            }

            .pagetable {
                height: 100%;
                width: 100%;
            }

            .logo {
                height: 50px;
                width: 100%;
                left: 50%;
            }

            .maintd {
                width: 100%;
                right: 50%;
                height: 100%;
            }

            .links {
                border-top: 3px solid #6699CC;
                border-bottom: 3px solid #6699CC;
                background-color: #336699;
                color: #E4E9EB;
            }

            .listtdleft {
                border-left: 1px solid #000000;
                border-right: 1px solid #000000;
                border-bottom: 1px solid #000000;
            }

            .listtd {
                border-right: 1px solid #000000;
                border-bottom: 1px solid #000000;

            }

            .headertdleft {
                border-left: 1px solid #000000;
                border-top: 3px solid #6699CC;
                border-bottom: 3px solid #6699CC;
                background-color: #336699;
                color: #E4E9EB;
            }

            .headertd {
                border-top: 3px solid #6699CC;
                border-bottom: 3px solid #6699CC;
                background-color: #336699;
                color: #E4E9EB;
            }

            .headertdright {
                border-right: 1px solid #000000;
                border-top: 3px solid #6699CC;
                border-bottom: 3px solid #6699CC;
                background-color: #336699;
                color: #E4E9EB;
            }

            a.toplinks {
                color: #E4E9EB;
                font-weight: bold;
            }
        </style>
        <script language="Javascript1.2" type="text/javascript">
            // function to alter form field values
            function set(item, waarde) {
                if (document.forms.pageform) {
                    eval("document.forms.pageform." + item + ".value='" + waarde + "'");
                }
            }

            function verzend() {
                if (document.forms.pageform) {
                    document.forms.pageform.submit();
                }
            }
        </script>
    </head>


    <body bgcolor="#ffffff" >
    <table id="secondary-links-table" summary="Navigation elements." border="0" cellpadding="0" cellspacing="0" class="pagetable">
    <tr>
        <td class="logo" align="center"><img src="logo.gif" width=200 height=80 alt="logo" border="0"/></td>
    </tr>
    <tr>
        <td class="links" align="center" valign="middle">
            <a class="toplinks" href="<?= $_SERVER["PHP_SELF"] ?>">[ Home ]</a>
            <a class="toplinks" href="Javascript:set('username','');set('action', 'edit_user');verzend();">[ New User ]</a>
            <a class="toplinks" href="Javascript:set('action', 'search');verzend();">[ Userlist ]</a>
            <a class="toplinks" href="http://pureuseradmin.sourceforge.net">[ Project Page ]</a>
        </td>
    </tr><tr>
    <td class="maintd" align="center">
    <div style="vertical-align: middle">
    <form name="pageform" id="pageform" method="post" action="index.php">
    <input type="hidden" name="action" value="<?= $_POST["action"] ?>"/>
    <input type="hidden" name="username" value="<?= $_POST["username"] ?>"/>
    <?
}

function html_footer()
{
    global $a;

    ?>
    </form>
    </div>
    </td>
    </tr>
    <tr>
        <td class="copyright" align="right">
            <a class="toplinks" href="<?= $a->settings["homepage"] ?>">PureUserAdmin <?= $a->settings["version"] ?></a>,
            by Michiel van Baak, &copy; 2004, released under the <a class="toplinks" href="http://www.gnu.org/copyleft/gpl.html">GPL.</a>
        </td>
    </tr></table>
    </body>
    </html>
    <?
}

function welcome()
{
    global $a;
    html_header("welcome");
    ?>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td class="headertdleft">Settings</td>
            <td class="headertdright">&nbsp;</td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Debugging:</td>
            <td class="listtd"><?= pureuseradmin::DEBUG ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Program version:</td>
            <td class="listtd"><?= $a->settings["version"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">FTP server address:</td>
            <td class="listtd"><?= $a->settings["ftp_hostname"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Admin email address:</td>
            <td class="listtd"><?= $a->settings["admin_email"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Database type:</td>
            <td class="listtd"><?= $a->settings["sql_type"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Database server:</td>
            <td class="listtd"><?= $a->settings["sql_server"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Database name:</td>
            <td class="listtd"><?= $a->settings["sql_dbase"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Database table:</td>
            <td class="listtd"><?= $a->settings["sql_table"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Password crypt method:</td>
            <td class="listtd"><?= $a->settings["pwcrypt"] ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Check homedir access:</td>
            <td class="listtd"><? echo ($a->settings["check_access"]) ? "yes" : "no"; ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Email user:</td>
            <td class="listtd"><? echo ($a->settings["notify_user"]) ? "yes" : "no"; ?></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Default uid:</td>
            <td class="listtd"><?= $a->uids[$a->settings["default_uid"]] ?> (<?= $a->settings["default_uid"] ?>)</td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">Default gid:</td>
            <td class="listtd"><?= $a->gids[$a->settings["default_gid"]] ?> (<?= $a->settings["default_gid"] ?>)</td>
        </tr>
    </table>
    <?
    html_footer();
}

function edit_user($username = "")
{
    global $a;
    if (strlen($username)) {
        $userget["username"] = $username;
        $userinfo = $a->get_user($userget);
        html_header("edit user");
        ?><input type="hidden" name="userinfo[update]" value="1"/><?
    } else {
        // new user
        html_header("new user");
        ?><input type="hidden" name="userinfo[update]" value="0"/><?
    }
    ?>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td class="headertdleft"><? if (strlen($userinfo)) { ?>Edit<? } else { ?>New<? } ?> User</td>
            <td class="headertdright">&nbsp;</td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">username:</td>
            <? if (strlen($userinfo)) { ?>
                <td class="listtd"><input type="hidden" name="userinfo[username]" value="<?= $userinfo["username"] ?>"/><?= $userinfo["username"] ?></td>
            <? } else { ?>
                <td class="listtd"><input type="text" name="userinfo[username]" value="<?= $userinfo["username"] ?>"/></td>
            <? } ?>
        </tr>
        <tr>
            <td class="listtdleft" align="right">password*:</td>
            <td class="listtd"><input type="password" name="userinfo[password]"/></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">retype&nbsp;password*:</td>
            <td class="listtd"><input type="password" name="userinfo[password1]"/></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">email:</td>
            <td class="listtd"><input type="text" name="userinfo[email]" value="<?= $userinfo['email'] ?>"/></td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">uid:</td>
            <?php if (empty($userinfo['uid'])) {
                $userinfo['uid'] = '65534';
            } ?>
            <td class="listtd">
                <select name="userinfo[uid]">
                    <?
                    if (!array_key_exists($userinfo["uid"], $a->uids)) {
                        ?>
                        <option value="<?= $userinfo["uid"] ?>" SELECTED><?= $userinfo["uid"] ?></option><?
                    }
                    foreach ($a->uids as $key => $val) {
                        ?>
                        <option value="<?= $key ?>" <? if ($userinfo["uid"] == $key) {
                            echo("SELECTED");
                        } ?>><?= $val ?></option><?
                    }
                    ?>
                    <option></option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">gid:</td>
            <?php if (empty($userinfo['gid'])) {
                $userinfo['gid'] = '65534';
            } ?>
            <td class="listtd">
                <select name="userinfo[gid]">
                    <?
                    if (!array_key_exists($userinfo["gid"], $a->gids)) {
                        ?>
                        <option value="<?= $userinfo["gid"] ?>" SELECTED><?= $userinfo["gid"] ?></option><?
                    }
                    foreach ($a->gids as $key => $val) {
                        ?>
                        <option value="<?= $key ?>" <? if ($userinfo["gid"] == $key) {
                            echo("SELECTED");
                        } ?>><?= $val ?></option><?
                    }
                    ?>
                    <option></option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="listtdleft" align="right">homedir:</td>
            <?php if (empty($userinfo['dir'])) {
                $userinfo['dir'] = '/srv/floorplanz/';
            } ?>
            <td class="listtd"><input type="text" name="userinfo[dir]" value="<?= $userinfo["dir"] ?>"/></td>
        </tr>
        <tr>
            <td colspan="2" class="listtdleft">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td width="33%" align="left"><a href="Javascript:set('action','save_user');verzend();">save</a></td>
                        <td width="33%" align="center"><a href="Javascript:set('action', 'gen_list');verzend();">back</a></td>
                        <td align="right"><a href="Javascript:set('action','delete_user');verzend();">delete</a></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    * leave blank to keep current password
    <?
    html_footer();
}

function gen_list($search = "", $start = 0)
{
    global $a;
    if (!$start) {
        $start = 0;
    }

    html_header("userlist");
    ?>
    <input type="hidden" name="start" value="<?= $start ?>">
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td class="headertdleft" width="33%">&nbsp;</td>
            <td class="headertd" width="33%">
                <div align="center">search</div>
            </td>
            <td class="headertdright">&nbsp;</td>
        </tr>
        <tr>
            <td class="listtdleft">searchstring</td>
            <td class="listtd"><input type="text" name="searchstring" value="<?= escape_html($search) ?>"></td>
            <td class="listtd"><a href="javascript:set('action','search');verzend();">go</a></td>
        </tr>
    </table>
    <br/>
    <table border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td class="headertdleft">username</td>
        <td class="headertd">uid</td>
        <td class="headertd">gid</td>
        <td class="headertdright">homedir</td>
    </tr>
    <?
    if ($search) {
        $sql_s = " WHERE username LIKE '%$search%' ";
    } else {
        $sql_s = "";
    }
    //how many users do we have

    $all_users = $a->get_all_users($search, $start);
    $usernr = $a->get_nr_users($search);
    foreach ($all_users as $user) {
        if ($a->settings["check_access"]) {
            $user_rights = $a->check_access($user["dir"], $user["uid"], $user["gid"]);
            if ($user_rights["error"]) {
                $right = $user_rights["error"];
            } else {
                if ($user_rights["write"]) {
                    $right = "user can read and write files in homedir";
                } elseif ($user_rights["read"]) {
                    $right = "user can only read files in homedir";
                } else {
                    $right = "<font color=\"red\">user has no access to homedir</font>";
                }
            }
        }
        ?>
        <tr>
            <td class="listtdleft"><a href="javascript:set('action','edit_user');set('username','<?= escape_html($user["username"]) ?>');verzend();"><?= escape_html($user["username"]) ?></a></td>
            <td class="listtd"><? echo $a->uids[$user["uid"]] ? $a->uids[$user["uid"]] : $user["uid"]; ?></td>
            <td class="listtd"><? echo $a->gids[$user["gid"]] ? $a->gids[$user["gid"]] : $user["gid"]; ?></td>
            <td class="listtd"><?= $user["dir"] ?> <? if ($a->settings["check_access"]) { ?>(<?= $right ?>)<? } ?></td>
        </tr>
        <?
    }
    if ($start || $start + $a->settings["page_size"] < $usernr) {
        ?>
        <tr>
            <td class="listtdleft" colspan="3">
                <? if ($start) { ?>
                    <a href="javascript:set('start','<?= ($start - $a->settings["page_size"]) ?>');verzend();">back <?= $a->settings["page_size"] ?> records</a>
                <? } ?>
            </td>
            <td class="listtd">
                <div align="right">
                    <? if ($start + $a->settings["page_size"] < $usernr) { ?>
                        <a href="javascript:set('start','<?= ($start + $a->settings["page_size"]) ?>');verzend();">forward <?= $a->settings["page_size"] ?> records</a>
                    <? } ?>
                </div>
            </td>
        </tr>
    <? } ?>
    </table><?

    html_footer();
}
