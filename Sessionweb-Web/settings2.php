<?php
session_start();
require_once('include/validatesession.inc');
require_once('config/db.php.inc');
require_once('include/header.php.inc');
require_once('include/db.php');
require_once('include/commonFunctions.php.inc');
require_once('include/session_common_functions.php.inc');
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

                    <table border="0" ><input
                        type="hidden" name="usernametoupdate" value="mattias">
                        <input type="hidden" name="command" value="changepassword">
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
                    <span id='change_personal_password_exe'>CHANGE PASSWORD</span>

                </div>

                <div class='divider_settings'></div>

                <div>
                    <a href="#" id='change_personal_settings_menu'>User settings</a>
                </div>
                <div id='change_personal_settings'>----</div>


                <h3>Manage Content</h3>

                <div>
                    <a href="#" id='team_menu'>Manage Team Entries</a>
                </div>
                <div id='add_remove_team'>
                    <div class='divider_settings'></div>
                    <p>Add Team</p>
                    <input type="text" size="50" value="" id="teamname"> <span id='add_team'>ADD</span>

                    <div class='divider_settings_noline'></div>
                    <p>Remove Team</p>

                    <div>
                        <select id='remove_team_select'></select> <span id='remove_team'>REMOVE</span>
                    </div>
                </div>

                <div class='divider_settings'></div>

                <div>
                    <a href="#" id='sprint_menu'>Manage Sprint Entries</a>
                </div>
                <div id='add_remove_sprint'>
                    <div class='divider_settings'></div>
                    <p>Add sprint</p>
                    <input type="text" size="50" value="" id="sprintname"> <span id='add_sprint'>ADD</span>

                    <div class='divider_settings_noline'></div>
                    <p>Remove sprint</p>

                    <div>
                        <select id='remove_sprint_select'></select> <span id='remove_sprint'>REMOVE</span>
                    </div>
                </div>

                <div class='divider_settings'></div>

                <div>
                    <a href="#" id='area_menu'>Manage Area Entries</a>
                </div>
                <div id='add_remove_area'>
                    <div class='divider_settings'></div>
                    <p>Add Area</p>
                    <input type="text" size="50" value="" id="areaname"> <span id='add_area'>ADD</span>

                    <div class='divider_settings_noline'></div>
                    <p>Remove Area</p>

                    <div>
                        <select id='remove_area_select'></select> <span id='remove_area'>REMOVE</span>
                    </div>
                </div>

                <div class='divider_settings'></div>

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
                    <span id='add_testenvironment'>ADD</span>

                    <div class='divider_settings_noline'></div>
                    <p>Remove testenvironment</p>

                    <div>
                        <select id='remove_testenvironment_select'></select> <span
                        id='remove_testenvironment'>REMOVE</span>
                    </div>
                </div>

                <div class='divider_settings'></div>

                <!--                <div>-->
                <!--                    <a href="#" id='custom_fields_menu'>Manage Custom Fields</a>-->
                <!--                </div>-->
                <!--                <div id='add_remove_custom_fields'>-->
                <!--                    <div id='add_custom_fields'>add team</div>-->
                <!--                    <div id='remove_custom_fields'>remove team</div>-->
                <!--                </div>-->

                <div class='divider_settings'></div>

                <h3>Site Settings</h3>

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
?>