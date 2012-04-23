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
    if($usersettings['list_view'] == "team")
        $team = "selected";
    if($usersettings['list_view'] == "all")
        $all = "selected";
    if($usersettings['list_view'] == "mine")
        $mine = "selected";

?>
    <select id="personal_changelistsettings_options" name="listsettings">
        <option value="all" <?php echo $all;?>>All sessions</option>
        <option value="mine" <?php echo $mine;?>>My own sessions</option>
        <option value="team" <?php echo $team;?>>My teams sessions</option>
    </select>

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
    Field: <?php echo $settings['custom1_name'].""; ?><br> <input type="text" size="50" value="" id="c1Name">
    <span class='settings_submit' id='add_customFieldsc1Entries'>ADD</span>
    <div>
        <select id='remove_customFieldsc1Entries_select'></select>
        <span class='settings_submit' id='remove_customFieldsc1Entries'>REMOVE</span>
    </div>
<!--    Field: --><?php //echo $settings['custom2_name'].""; ?><!--<br> <input type="text" size="50" value="" id="c2Name">-->
<!--    <span class='settings_submit' id='add_customFieldsc2Entries'>ADD</span>-->
<!--    <div>-->
<!--        <select id='remove_customFieldsc2Entries_select'></select>-->
<!--        <span class='settings_submit' id='remove_customFieldsc2Entries'>REMOVE</span>-->
<!--    </div>-->
<!--    Field: --><?php //echo $settings['custom3_name'].""; ?><!--<br> <input type="text" size="50" value="" id="c3Name">-->
<!--    <span class='settings_submit' id='add_customFieldsc3Entries'>ADD</span>-->
<!--    <div>-->
<!--        <select id='remove_customFieldsc3Entries_select'></select>-->
<!--        <span class='settings_submit' id='remove_customFieldsc3Entries'>REMOVE</span>-->
<!--    </div>-->
    <div class='divider_settings_noline'></div>

    <div class='divider_settings'></div>
</div>

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

<a href="settings.php">Old settings page</a>
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

?>