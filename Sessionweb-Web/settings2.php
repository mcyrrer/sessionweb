<?php
session_start();
require_once('include/validatesession.inc');
require_once('config/db.php.inc');
require_once('include/header.php.inc');
require_once('include/db.php');
require_once('include/commonFunctions.php.inc');
require_once('include/session_common_functions.php.inc');

$usersettings = getUserSettings();
$settings = getSettings();
list($c1, $c2, $c3, $ce1, $ce2, $ce3) = getCustomFieldProperties($settings);
?>


<h1>Settings</h1>
<div id="msgdiv"></div>

<div id="content">

<table border=0>
<tr>
<td width="800" valign="top">
<h3>My Settings</h3>

<div>
    <a href="#" id='change_personal_password_menu'>Change password</a>
</div>
<div id='change_personal_password'>

    <table border="0">
        <tr>
            <td align="left">
                Old password
            </td>
            <td><input type="password" size="50" value="" id="changepasswordold">
            </td>
        </tr>
        <tr>
            <td align="left">
                New password
            </td>
            <td><input type="password" size="50" value="" id="changepassword1">
            </td>
        </tr>
        <tr>
            <td align="left">
                Retype password
            </td>
            <td><input type="password" size="50" value="" id="changepassword2">
            </td>
        </tr>
    </table>
    <span class='settings_submit' id='change_personal_password_exe'>CHANGE PASSWORD</span>

</div>


<div>
    <a href="#" id='change_personal_settings_menu'>User settings</a>
</div>
<div id='change_personal_settings'>
    <h3>Settings for List sessions</h3>
    <?php
    $all = "";
    $team = "";
    $mine = "";
    if ($usersettings['list_view'] == "team")
        $team = "selected";
    if ($usersettings['list_view'] == "all")
        $all = "selected";
    if ($usersettings['list_view'] == "mine")
        $mine = "selected";
    if ($usersettings['autosave'] == 1)
        $autosaveCheckedHtml = "checked=\"checked\"";
    else
        $autosaveCheckedHtml = "";

    ?>
    <select id="personal_changelistsettings_options" name="listsettings">
        <option value="all" <?php echo $all;?>>All sessions</option>
        <option value="mine" <?php echo $mine;?>>My own sessions</option>
        <option value="team" <?php echo $team;?>>My teams sessions</option>
    </select>
    <h3>Autosave when edit sessions</h3>

    Enable: <input type="checkbox" id="autosave" <?php echo $autosaveCheckedHtml;?> value="checked">

    <h3>Settings for new session</h3><br>
    Select default team:
    <?php echoTeamSelect($usersettings['default_team'], true, false, "personal_select_team");?>

    <br>
    Select default sprint:
    <?php echoSprintSelect($usersettings['default_sprint'], false, "personal_select_sprint");?>

    <br>
    Select default area:
    <?php echoAreaSelectSingel($usersettings['default_area'], false, "personal_select_area");?>

    <div>
        <span class='settings_submit' id='change_personal_settings_exe'>CHANGE SETTINGS</span>
    </div>
    <!--                    <a href="settings.php">Old settings page</a>-->
</div>

<div id="contentmenu">
    <h3 id="manage_content">Manage Content</h3>

    <div>
        <a href="#" id='team_menu'>Manage Team Entries</a>
    </div>
    <div id='add_remove_team'>
        <div class='divider_settings'></div>
        <p>Add Team</p>
        <input type="text" size="50" value="" id="teamname"> <span class='settings_submit' id='add_team'>ADD</span>

        <div class='divider_settings_noline'></div>
        <p>Remove Team</p>

        <div>
            <select id='remove_team_select'></select> <span class='settings_submit' id='remove_team'>REMOVE</span>
        </div>
        <div class='divider_settings'></div>

    </div>


    <div>
        <a href="#" id='sprint_menu'>Manage Sprint Entries</a>
    </div>
    <div id='add_remove_sprint'>
        <div class='divider_settings'></div>
        <p>Add sprint</p>
        <input type="text" size="50" value="" id="sprintname"> <span class='settings_submit' id='add_sprint'>ADD</span>

        <div class='divider_settings_noline'></div>
        <p>Remove sprint</p>

        <div>
            <select id='remove_sprint_select'></select> <span class='settings_submit' id='remove_sprint'>REMOVE</span>
        </div>
        <div class='divider_settings'></div>
    </div>


    <div>
        <a href="#" id='area_menu'>Manage Area Entries</a>
    </div>
    <div id='add_remove_area'>
        <div class='divider_settings'></div>
        <p>Add Area</p>
        <input type="text" size="50" value="" id="areaname"> <span class='settings_submit' id='add_area'>ADD</span>

        <div class='divider_settings_noline'></div>
        <p>Remove Area</p>

        <div>
            <select id='remove_area_select'></select> <span class='settings_submit' id='remove_area'>REMOVE</span>
        </div>
        <div class='divider_settings'></div>
    </div>


    <div>
        <a href="#" id='testenvironments_menu'>Manage Test Environments Entries</a>
    </div>
    <div id='add_remove_testenvironment'>
        <div class='divider_settings'></div>
        <p>Add testenvironment</p>
        Name:<br> <input type="text" size="50" value="" id="teName"><br>
        Web page with information about software running on environment:<br><i>By adding this sessionweb
        will be able to automatically fetch running software from this environment</i><br>
        <input type="text" size="50" value="" id="teUrl"><br>
        Username:<br><input type="text" size="50" value="" id="teUser"><br>
        Password:<br><input type="text" size="50" value="" id="tePassword"><br>
        <span class='settings_submit' id='add_testenvironment'>ADD</span>

        <div class='divider_settings_noline'></div>
        <p>Remove testenvironment</p>

        <div>
            <select id='remove_testenvironment_select'></select> <span
            id='remove_testenvironment'>REMOVE</span>
        </div>
        <div class='divider_settings'></div>
    </div>

    <div>
        <a href="#" id='customFieldsEntries_menu'>Manage Custom Fields Entries</a>
    </div>
    <div id='customFieldsEntries_testenvironment'>
        <div class='divider_settings'></div>
        <p>Add Item</p>
        <?php
        echoCustomFieldAddItem("custom1_name", 1, $settings);
        echoCustomFieldAddItem("custom2_name", 2, $settings);

        echoCustomFieldAddItem("custom3_name", 3, $settings);

        ?>

        <div class='divider_settings_noline'></div>

        <div class='divider_settings'></div>
    </div>
</div>
<div id="adminmenu">
    <h3 id="site_settings">Site Settings</h3>

    <div>
        <a href="#" id='customfields_menu'>Add/Update Custom Fields Names</a>
    </div>
    <div id='manage_customfields'>
        <?php
        list($c1, $c2, $c3, $ce1, $ce2, $ce3) = getCustomFieldProperties($settings);

        ?>
        <div class='divider_settings'></div>
        Custom field 1:<br> <input type="text" size="50" value="<?php echo $settings['custom1_name'];?>" id="cf1Name">
        Multiselect:<input type="checkbox" id="cf1multiselect" <?php echo $c1;?> value="1">
        Enabled:<input type="checkbox" id="cf1enabled" <?php echo $ce1;?> value="1">
        <br>
        Custom field 2:<br> <input type="text" size="50" value="<?php echo $settings['custom2_name'];?>" id="cf2Name">
        Multiselect:<input type="checkbox" id="cf2multiselect" <?php echo $c2;?> value="1">
        Enabled:<input type="checkbox" id="cf2enabled" <?php echo $ce2;?> value="1"><br>
        Custom field 3:<br> <input type="text" size="50" value="<?php echo $settings['custom3_name'];?>" id="cf3Name">
        Multiselect:<input type="checkbox" id="cf3multiselect" <?php echo $c3;?> value="1">
        Enabled:<input type="checkbox" id="cf3enabled" <?php echo $ce3;?> value="1"><br>
        <span class='settings_submit' id='update_customfields'>ADD/UPDATE</span>

        <!--                    <div class='divider_settings_noline'></div>-->

        <div class='divider_settings'></div>
    </div>

    <div>
        <a href="#" id='appconfig_menu'>Application Configuration</a>
    </div>
    <div id='add_remove_appconfig'>
        <div class='divider_settings'></div>
        <p>Configure application</p>

        <form id="appForm">
            <table width="*" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>Normalized Sessions time(min)
                    </td>
                    <td><input type="text" size="50" name="normlizedsessiontime" id="normlizedsessiontime">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><span class='italic'>If a session is 120min and the normalized Sessions time is 90 the session is counted as 1.33 sessions (120/90).
            This is used to be able to compare testers throughput with each other.</span>
                    </td>
                </tr>
                <tr>
                    <td>Defect Managment System URL
                    </td>
                    <td><input type="text" size="50" value="" name="url_to_dms" id="url_to_dms">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><span class='italic'>e.g. "http://code.google.com/p/sessionweb/issues/detail?id="  issue number will be added at the end automatically.</span>
                    </td>
                </tr>
                <tr>
                    <td>Requirement Management System URL
                    </td>
                    <td><input type="text" size="50" value="" name="url_to_rms" id="url_to_rms">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><span class='italic'>e.g. "http://code.google.com/p/sessionweb/issues/detail?id="  issue number will be added at the end automatically.</span>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><b>Activate Modules</b>
                    </td>
                </tr>
                <tr>
                    <td>Team
                    </td>
                    <td><input type="checkbox" id="app_team" name="team" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>Sprint
                    </td>
                    <td><input type="checkbox" id="app_sprint" name="sprint" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>Area
                    </td>
                    <td><input type="checkbox" id="app_area" name="area" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>Test Environment
                    </td>
                    <td><input type="checkbox" id="app_env" name="env" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>Public view
                    </td>
                    <td><input type="checkbox" id="app_publicview" name="publicview" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><span class='italic'>Public view makes it possible for a person that does not have a Sessionweb account to view a session.
            This is made by sharing the public link that contains a unique key for that specific session.</span>
                    </td>
                </tr>
                <tr>
                    <td>Word Cloud in session view
                    </td>
                    <td><input type="checkbox" id="app_wordcloud" name="wordcloud" checked="checked" value="checked">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><span class='italic'>In some cases a word cloud makes it easier to get a quick overview of a session.
            A word cloud is a presentation of the top 100 words used in the session and the higher the count is the larger font is used.</span>
                    </td>
                </tr>
            </table>
        </form>
        <span class='settings_submit' id='update_appconfig'>UPDATE</span>

        <div class='divider_settings'></div>
    </div>

    <div>
        <a href="#" id='bulkclosesessions_menu'>Bulk close sessions</a>
    </div>
    <div id='bulkclosesessions'>
        <div class='divider_settings'></div>
        <p>Bulk close sessions</p>
        This command will set all sessions older then the given date that are in state EXECUTED to closed.<br>
        Close sessions older then:<br> <input type="text" size="10" value="" id="datepicker_bulkclosesessions"><br>
        <span class='settings_submit' id='bulkclosesessions_close'>CLOSE</span>

    </div>

    <div>
        <a href="#" id='adduser_menu'>Add user</a>
    </div>
    <div id='adduser'>
        <div class='divider_settings'></div>
        <p>Add a new user to sessionweb<br>
        <span id='usermessages'></span></p><br>

        <form id="adduserForm">
        Full name:<input id="user_fullname"type="text" size="40" value="" name="fullname"><span class="italic">Minimum length is 4 chars</span><br>
        User name:<input id="user_username" type="text" size="40" value="" name="username"><span class="italic">Minimum length is 4 chars</span><br>
        Password:<input id="user_pw1" type="password" size="40" value="" name="pw1"> <span class="italic">Minimum length is 6 chars</span> <br>
        Retype password:<input id="user_pw2" type="password" size="40" value="" name="pw2"><br>
        <?php
            if ($_SESSION['settings']['team'] == 1) {
                echo "Team:";
                echoTeamSelect("");
                echo "<br>";
            }
            ?>
        Admin: <input type="checkbox" name="admin" value="yes">
        Superuser: <input type="checkbox" name="superuser" value="yes"><br>
        <span class='settings_submit' id='adduser_add'>ADD</span>
        </form>

    </div>

    <a href="settings.php?command=listusers">User management</a><br>
    <!--                <div>-->
    <!--                    <a href="#" id='configuration_menu'>Manage Custom Fields</a>-->
    <!--                </div>-->
    <!--                <div id='change_configuration'>-->
    <!--                    ----->
    <!--                </div>-->
    <!---->
    <!--                <div class='divider_settings'></div>-->
    <!---->
    <!--                <div>-->
    <!--                    <a href="#" id='systemcheck_menu'>System Check</a>-->
    <!--                </div>-->
    <!--                <div id='systemcheck'>-->
    <!--                    ----->
    <!--                </div>-->
    <a href="log.php">View Sessionweb log file</a><br>
    <a href="logsql.php">View Sessionweb sql log file</a><br>
    <a href="systemcheck.php">Sessionweb system check</a>


</div>

</td>
<td width="300" valign="top">
    <h3>Log</h3>

    <div id='log'></div>
</td>
</tr>
</table>

</div>

<?php
require_once('include/footer.php.inc');

function getCustomFieldProperties($settings)
{
    $c1 = "";
    $c2 = "";
    $c3 = "";
    if ($settings['custom1_multiselect'])
        $c1 = "checked='checked'";
    if ($settings['custom2_multiselect'])
        $c2 = "checked='checked'";
    if ($settings['custom3_multiselect'])
        $c3 = "checked='checked'";
    $ce1 = "";
    $ce2 = "";
    $ce3 = "";
    if ($settings['custom1'])
        $ce1 = "checked='checked'";
    if ($settings['custom2'])
        $ce2 = "checked='checked'";
    if ($settings['custom3'])
        $ce3 = "checked='checked'";
    return array($c1, $c2, $c3, $ce1, $ce2, $ce3);
    return array($c1, $c2, $c3, $ce1, $ce2, $ce3);
}

function echoCustomFieldAddItem($customFieldsName, $customFieldsNo, $settings)
{
    if ($settings['custom' . $customFieldsNo] == 1) {
        echo "Field: " . $settings[$customFieldsName] . "<br> <input type='text' size='50' value='' id='c" . $customFieldsNo . "Name'>";
        echo "    <span class='settings_submit' id='add_customFieldsc" . $customFieldsNo . "Entries'>ADD</span>";
        echo "    <div>";
        echo "        <select id='remove_customFieldsc" . $customFieldsNo . "Entries_select'></select>";
        echo "        <span class='settings_submit' id='remove_customFieldsc" . $customFieldsNo . "Entries'>REMOVE</span>";
        echo "    </div>";
    }
}

?>