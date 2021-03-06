<?php
require_once('classes/autoloader.php');
require_once('include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

include("include/header.php.inc");

echo "<h1>Settings</h1>\n";

//echoMenu();

executeCommand();

include("include/footer.php.inc");


//*************************************************************************************
//Function is located below
//*************************************************************************************s

function executeCommand()
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();
    //Administartor Commands
    if ($_SESSION['useradmin'] == 1) {
        if (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "listusers") == 0) {
            echoAllUsersInfo(true);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "adduser") == 0) {
            echoAddUser();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertusertodb") == 0) {
            createNewUser();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "addteam") == 0) {
            echoAddTeamName();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertteamnametodb") == 0) {
            insertTeamNameToDb($_REQUEST["teamtname"]);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "changeusersettings") == 0) {
            if (!isset($_REQUEST["usernametoupdate"])) {
                $_REQUEST["usernametoupdate"] = "";
            }
            if (!isset($_REQUEST["active"])) {
                $_REQUEST["active"] = "";
            }
            if (!isset($_REQUEST["admin"])) {
                $_REQUEST["admin"] = "";
            }
            if (!isset($_REQUEST["superuser"])) {
                $_REQUEST["superuser"] = "";
            }
            if (!isset($_REQUEST["team"])) {
                $_REQUEST["team"] = "";
            }
            echo "<br>";

            updateUserSettings($_REQUEST["usernametoupdate"], $_REQUEST["active"], $_REQUEST["admin"], $_REQUEST["superuser"], $_REQUEST["team"]);

        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "userinfo") == 0) {
            echoChangeUserInfo($_GET["user"]);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "config") == 0) {
            echoChangeConfig();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "updateconfig") == 0) {
            updateConfig();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "addenv") == 0) {
            echoAddEnvironment();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertenvname") == 0) {
            insertEnvironmentNameToDb();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "addsprint") == 0) {
            echoAddSprintName();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertsprintnametodb") == 0) {
            insertSprintNameToDb($_REQUEST["sprintname"]);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "systemcheck") == 0) {
            echo "<h1>System check</h1>";


            checkFoldersForRW();
            checkForMaxAttachmentSize();
            debugInfo();

            echo "<div><a href='include/phpinfo.php' >Get php info</a></div>";
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "customfileds") == 0) {
            echoManageCustomFileds();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertcustomfields") == 0) {
            insertCustomFieldsSettingsToDb();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertcustomfieldsadd") == 0) {
            insertCustomFieldNameToDb();
        }


    }
    //SuperUser Commands
    if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {
        if (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "addteamsprint") == 0) {
            echoAddTeamSprintName();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertareaname") == 0) {
            insertAreaNameToDb($_REQUEST["areaname"]);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertareaname") == 0) {
            insertAreaNameToDb($_REQUEST["areaname"]);
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "addarea") == 0) {
            echoAddAreaName();
        } elseif (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "insertteamsprintnametodb") == 0) {
            insertTeamSprintNameToDb($_REQUEST["teamsprintname"]);
        }


    }

    //Common commands
    if (strcmp($_REQUEST["command"], "changepassword") == 0) {
        updateUserPassword($_REQUEST["usernametoupdate"], $_REQUEST["swpassword1"], $_REQUEST["swpassword2"]);
    }
    if (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "changepassword") == 0) {

        echoChangePassword($_SESSION['username']);
    }
    if (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "listsettings") == 0) {

        echoChangeListSettings();
    }
    if (isset($_REQUEST["command"]) && strcmp($_REQUEST["command"], "changelistsettings") == 0) {
        updateUserSettingsForLoginUser();
    }


}

function debugInfo()
{
    echo "Browser: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
    echo "PHP Memory peek usage: " . (memory_get_peak_usage() / 1024 / 1024) . "mb<br>";
}

function insertCustomFieldNameToDb()
{

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    $table = "custom" . $_POST['id'];
    $name = $_POST['custom_name'];

    $sql = "INSERT INTO custom_items (`tablename`, `name`) VALUES ('$table','$name')";
    $dbm->executeQuery($con, $sql);


    echo "<br><br>Name inserted to database";
}

function insertCustomFieldsSettingsToDb()
{

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    if ($_REQUEST['custom1_enabled'] == 1)
        $c1_enable = '1';
    else
        $c1_enable = '0';
    if ($_REQUEST['custom2_enabled'] == 1)
        $c2_enable = '1';
    else
        $c2_enable = '0';
    if ($_REQUEST['custom2_enabled'] == 1)
        $c3_enable = '1';
    else
        $c3_enable = '0';

    if ($_POST['custom1_multiselect'] == 1)
        $c1ms_enable = '1';
    else
        $c1ms_enable = '0';
    if ($_POST['custom2_multiselect'] == 1)
        $c2ms_enable = '1';
    else
        $c2ms_enable = '0';
    if ($_POST['custom3_multiselect'] == 1)
        $c3ms_enable = '1';
    else
        $c3ms_enable = '0';

    $sql = "
        UPDATE settings
        SET    custom1 = $c1_enable,
               custom1_name = '" . $_REQUEST['custom1_name'] . "',
               custom1_multiselect = $c1ms_enable,
               custom2 = $c2_enable,
               custom2_name = '" . $_REQUEST['custom2_name'] . "',
               custom2_multiselect = $c2ms_enable,
               custom3 = $c3_enable,
               custom3_name = '" . $_REQUEST['custom3_name'] . "',
               custom3_multiselect = $c3ms_enable
        WHERE  id = '1'";
    $dbm->executeQuery($con, $sql);

    echo "Settings updated";
}

function echoManageCustomFileds()
{
    $settings = ApplicationSettings::getSettings();

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    echo "<h1>Custom fields</h1>";
    echo "<h2>Fields setup</h2>";
    echo "If you change these values you will need to log out and in again to enable the changes. ";
    echo "<form name=\"customfileds\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertcustomfields\">\n";
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

    echo "<table border='1' bordercolor='' bgcolor=''>
            <THEAD>
                <TR>
                  <TH>ID</TH>
                  <TH>Name</TH>
                  <TH>Enable multiselect</TH>
                  <TH>Enabled</TH>
                </TR>
            </THEAD>
            <tr>
            <td>1</td>
            <td> <input type='text' name='custom1_name' value='" . $settings['custom1_name'] . "'/></td>
            <td> <input type='checkbox' name='custom1_multiselect' value='1' $c1 /> </td>
            <td> <input type='checkbox' name='custom1_enabled' value='1' $ce1 /></td>
            </tr>
            <tr>
            <td>2</td>
            <td> <input type='text' name='custom2_name' value='" . $settings['custom2_name'] . "'/> </td>
            <td> <input type='checkbox' name='custom2_multiselect' value='1' $c2 /> </td>
            <td> <input type='checkbox' name='custom2_enabled' value='1' $ce2 /></td>
            </tr>
            <tr>
            <td>3</td>
            <td> <input type='text' name='custom3_name' value='" . $settings['custom3_name'] . "'/> </td>
            <td> <input type='checkbox' name='custom3_multiselect' value='1' $c3 /> </td>
            <td> <input type='checkbox' name='custom3_enabled' value='1' $ce3 /></td>
            </tr>
            </table>";
    echo "<input type='submit' value='Submit' />";
    echo "</form>";

    echo "<h2>Add value to field</h2>";
    echo "<p>First you have to add filed above before you can add value to it.</p>";
    echo "<form name=\"customfiledsadd\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertcustomfieldsadd\">\n";


    echo "<select id='select_customname' name='id'>
	<option value='1'>" . $settings['custom1_name'] . "</option>
	<option value='2'>" . $settings['custom2_name'] . "</option>
	<option value='3'>" . $settings['custom3_name'] . "</option>
    </select>
    <input type='text' name='custom_name'/>";

    echo "<input type='submit' value='Submit' />";
    echo "</form>";
}

function echoAddEnvironment()
{
    echo "<h2>Add new test environment name</h2>\n";
    echo "<form name=\"envname\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertenvname\">\n";
    echo "<p>New environment name</p>\n";
    echo "<p><input type=\"text\" size=\"50\" value=\"\" name=\"envname\"></p>\n";
    echo "<h3>Optional information</h3>\n";
    echo "<p>Web page with information about software running on environment:<i>By adding this sessionweb will be able to autofetch running software from environment</i></p>\n";
    echo "<p><input type=\"text\" size=\"50\" value=\"\" name=\"envautofetchurl\"></p>\n";
    echo "<p>Username:<i>(fill in if page is password protected)</i></p>\n";
    echo "<p><input type=\"text\" size=\"50\" value=\"\" name=\"envusername\"></p>\n";
    echo "<p>Password:<i>(fill in if page is password protected)</i></p>\n";
    echo "<p><input type=\"password\" size=\"50\" value=\"\" name=\"envpassword\"></p>\n";

    echo "<p><input align=left type=\"submit\" value=\"Add environment\" /></p>\n";
    echo "</form>\n";
}

function echoChangeConfig()
{

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    $sqlSelect = "SELECT * FROM settings";

    $result = $dbm->executeQuery($con, $sqlSelect);

    if (!$result) {
        echo "echoChangeConfig: " . mysqli_error($con) . "<br/>";
    } else {
        $row = mysqli_fetch_array($result);
        echo "<h4>Change Application Configuration</h4>\n";
        echo "<form name=\"teamname\" action=\"settings.php\" method=\"POST\">\n";
        echo "<input type=\"hidden\" name=\"command\" value= \"updateconfig\">\n";
        echo "	<table width=\"*\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        echo "    <tr>\n";
        echo "        <td>\n";
        echo "        </td>\n";
        echo "        <td><b>Common Settings</b>\n";
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Normalized Sessions time(min)\n";
        echo "        </td>\n";
        echo "        <td> <input type=\"text\" size=\"50\" value=\"" . $row[normalized_session_time] . "\" name=\"normlizedsessiontime\">\n";
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Defect Managment System URL\n";
        echo "        </td>\n";
        echo "        <td> <input type=\"text\" size=\"50\" value=\"" . $row[url_to_dms] . "\" name=\"url_to_dms\">\n";
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Requirement Management System URL\n";
        echo "        </td>\n";
        echo "        <td> <input type=\"text\" size=\"50\" value=\"" . $row[url_to_rms] . "\" name=\"url_to_rms\">\n";
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>\n";
        echo "        </td>\n";
        echo "        <td><b>Activate Modules</b>\n";
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Team\n";
        echo "        </td>\n";
        if ($row[team] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"team\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"team\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Sprint\n";
        echo "        </td>\n";
        if ($row[sprint] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"sprint\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"sprint\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Team sprint\n";
        echo "        </td>\n";
        if ($row[teamsprint] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"teamsprint\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"teamsprint\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Area\n";
        echo "        </td>\n";
        if ($row[area] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"area\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"area\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Test Environment\n";
        echo "        </td>\n";
        if ($row[testenvironment] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"env\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"env\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "        <td>Public view\n";
        echo "        </td>\n";
        if ($row[publicview] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"publicview\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"publicview\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";

        echo "        <td>Word Cloud in session view\n";
        echo "        </td>\n";
        if ($row[wordcloud] == 1) {
            echo "        <td> <input type=\"checkbox\" name=\"wordcloud\" checked=\"checked\" value=\"checked\" >\n";
        } else {
            echo "        <td> <input type=\"checkbox\" name=\"wordcloud\" value=\"checked\" >\n";
        }
        echo "        </td>\n";
        echo "    </tr>\n";
        echo "</table>\n";
        echo "            <input align=left type=\"submit\" value=\"Change settings\" />\n";
        echo "</form>\n";
    }


}

function echoMenu()
{
    if ($_SESSION['useradmin'] == 1) {
        echo "<div>Admin menu: ";
        if ($_SESSION['settings']['team'] == 1) {
            echo "<a href=\"settings.php?command=addteam\">Add team</a> | ";
        }
        if ($_SESSION['settings']['sprint'] == 1) {
            echo "<a id=\"url_addsprint\" href=\"settings.php?command=addsprint\">Add sprintname</a> | ";
        }
        echo "<a id=\"url_listusers\" href=\"settings.php?command=listusers\">List users</a> | ";
        echo "<a id=\"url_adduser\" href=\"settings.php?command=adduser\">Add user</a> | ";
        echo "<a id=\"url_addenv\" href=\"settings.php?command=addenv\">Add test environment</a> | ";
        echo "<a id=\"url_configuration\" href=\"settings.php?command=config\">Configuration</a> | ";
        echo "<a id=\"url_systemcheck\" href=\"settings.php?command=systemcheck\">System Check</a> | ";

        //echo "<a id=\"url_cvs\" href=\"cvs.php\">Export to cvs</a> | ";
        echo "<a id=\"url_custom\" href=\"settings.php?command=customfileds\">Manage custom fields</a> | ";
        echo "</div>";
    }
    if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {
        echo "<div>Superuser menu:  ";
        if ($_SESSION['settings']['area'] == 1) {
            echo "<a id=\"url_addarea\" href=\"settings.php?command=addarea\">Add area</a> | ";
        }
        if ($_SESSION['settings']['teamsprint'] == 1) {
            echo "<a id=\"url_addteamsprint\" href=\"settings.php?command=addteamsprint\">Add team sprintname</a> | ";
        }
        echo "</div>";
    }
    echo "<div>User menu: <a id=\"url_changepassword\" href=\"settings.php?command=changepassword\">Change password</a> | <a id=\"url_listsettings\" href=\"settings.php?command=listsettings\">User settings</a></div>";
}

function echoAddTeamName()
{
    echo "<h2>Add new team name</h2>\n";
    echo "<form name=\"teamname\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertteamnametodb\">\n";
    echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            New team name\n";
    echo "        </td>\n";
    echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"teamtname\">\n";
    echo "        </td>\n";
    echo "        <td align=\"left\">\n";
    echo "            <input align=left type=\"submit\" value=\"Add team\" />\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>";
    echo "</form>\n";
}

function insertTeamNameToDb($teamName)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $teamName = mysqli_real_escape_string($con,$teamName);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO teamnames ";
    $sqlInsert .= "            (`teamname`) ";
    $sqlInsert .= "VALUES      ('$teamName')";


    $result = $dbm->executeQuery($con, $sqlInsert);

    if (!$result) {
        echo "InsertTeamNameToDb: " . mysqli_error($con) . "<br/>";
    } else {
        echo "<p>Team name $teamName added to database</p>\n";
    }

}

function insertEnvironmentNameToDb()
{
    $envName = $_REQUEST["envname"];
    $envautofetchurl = $_REQUEST["envautofetchurl"];
    $envusername = $_REQUEST["envusername"];
    $envpassword = $_REQUEST["envpassword"];

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $areaName = mysqli_real_escape_string($con,$envName);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO testenvironment ";
    $sqlInsert .= "            (name, ";
    $sqlInsert .= "             url, ";
    $sqlInsert .= "             username, ";
    $sqlInsert .= "             PASSWORD) ";
    $sqlInsert .= "VALUES      ('$envName', ";
    $sqlInsert .= "             '$envautofetchurl', ";
    $sqlInsert .= "             '$envusername', ";
    $sqlInsert .= "             '$envpassword') ";


    $result = $dbm->executeQuery($con, $sqlInsert);

    if (!$result) {
        if (mysqli_errno($con) == 1062) {
            echo "<p>Test environment $envName not added since it already exists in database.</p>";
        } else {
            echo "insertEnvironmentNameToDb: " . mysqli_error($con) . "<br>";
            echo "Mysql error no: " . mysqli_errno($con) . "<br>";
        }
    } else {
        echo "<p>Test environment $envName added to database</p>\n";
    }


}

function insertAreaNameToDb($areaName)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $areaName = mysqli_real_escape_string($con,$areaName);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO areas ";
    $sqlInsert .= "            (`areaname`) ";
    $sqlInsert .= "VALUES      ('$areaName')";


    $result = $dbm->executeQuery($con, $sqlInsert);

    if (!$result) {
        if (mysqli_errno($con) == 1062) {
            echo "<p>Area $areaName not added since it already exists in database.</p>";
        } else {
            echo "insertAreaNameToDb: " . mysqli_error($con) . "<br>";
            echo "Mysql error no: " . mysqli_errno($con) . "<br>";
        }
    } else {
        echo "<p>Area name $areaName added to database</p>\n";
    }


}


function echoAddSprintName()
{
    echo "<h2>Add new sprint name</h2>\n";
    echo "<form name=\"sprintname\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertsprintnametodb\">\n";
    echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            New sprint name\n";
    echo "        </td>\n";
    echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"sprintname\">\n";
    echo "        </td>\n";
    echo "        <td align=\"left\">\n";
    echo "            <input align=left type=\"submit\" value=\"Add name\" />\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>";
    echo "</form>\n";
}

function echoAddAreaName()
{
    echo "<h2>Add new area name</h2>\n";
    echo "<form name=\"areaname\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertareaname\">\n";
    echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            New area name\n";
    echo "        </td>\n";
    echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"areaname\">\n";
    echo "        </td>\n";
    echo "        <td align=\"left\">\n";
    echo "            <input align=left type=\"submit\" value=\"Add area\" />\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>";
    echo "</form>\n";
}

function echoAddTeamSprintName()
{
    echo "<h2>Add new team sprint name</h2>\n";
    echo "<form name=\"teamsprintname\" action=\"settings.php\" method=\"POST\">\n";
    echo "<input type=\"hidden\" name=\"command\" value= \"insertteamsprintnametodb\">\n";
    echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            New team sprint name\n";
    echo "        </td>\n";
    echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"teamsprintname\">\n";
    echo "        </td>\n";
    echo "        <td align=\"left\">\n";
    echo "            <input align=left type=\"submit\" value=\"Add name\" />\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>";
    echo "</form>\n";
}

function insertSprintNameToDb($sprintName)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $sprintName = mysqli_real_escape_string($con,$sprintName);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO sprintnames ";
    $sqlInsert .= "            (`sprintname`) ";
    $sqlInsert .= "VALUES      ('$sprintName')";


    $result = $dbm->executeQuery($con, $sqlInsert);

    if (!$result) {
        echo "InsertSprintNameToDb: " . mysqli_error($con) . "<br/>";
    } else {
        echo "<p>Sprint name $sprintName added to database</p>\n";
    }


}

function insertTeamSprintNameToDb($teamsprintName)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    $teamsprintName = mysqli_real_escape_string($con,$teamsprintName);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO teamsprintnames ";
    $sqlInsert .= "            (`teamsprintname`) ";
    $sqlInsert .= "VALUES      ('$teamsprintName')";


    $result = $dbm->executeQuery($con, $sqlInsert);

    if (!$result) {
        echo "insertTeamSprintNameToDb: " . mysqli_error($con) . "<br/>";
    } else {
        echo "<p>Team sprint name $teamsprintName added to database</p>\n";
    }


}

function echoChangeListSettings()
{

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $usersettings = UserSettings::getUserSettings();

    echo "<h2>Change user settings</h2>";
    echo "<form name=\"userinfo\" action=\"settings.php\" method=\"POST\">";

    echo "<h3>Settings for List sessions</h3>";

    echo "Choose default filter for  the \"List sessions\" page:<input type=\"hidden\" name=\"command\" value= \"changelistsettings\">\n";

    echo "<select id=\"changelistsettings_options\" name=\"listsettings\">\n";
    if ($usersettings['list_view'] == "mine") {
        echo "<option value=\"all\" >All sessions</option>\n";
        echo "<option value=\"mine\" selected>My own sessions</option>\n";
        echo "<option value=\"team\">My teams sessions</option>\n";
    } elseif ($usersettings['list_view'] == "team") {
        echo "<option value=\"all\" >All sessions</option>\n";
        echo "<option value=\"mine\">My own sessions</option>\n";
        echo "<option value=\"team\" selected>My teams sessions</option>\n";
    } else {
        echo "<option value=\"all\" selected>All sessions</option>\n";
        echo "<option value=\"mine\">My own sessions</option>\n";
        echo "<option value=\"team\">My teams sessions</option>\n";

    }

    echo "</select>\n";
    echo "<h3>Settings for new session</h3>";

    if ($_SESSION['settings']['team'] == 1) {
        echo "<br>\n";
        echo "Select default team:\n";
        echoTeamSelect($usersettings['default_team']);
    }

    if ($_SESSION['settings']['sprint'] == 1) {
        echo "<br>\n";
        echo "Select default sprint:\n";
        echoSprintSelect($usersettings['default_sprint']);
    }

    if ($_SESSION['settings']['teamsprint'] == 1) {
        echo "<br>\n";
        echo "Select default team sprint:\n";
        echoTeamSprintSelect($usersettings['default_teamsprint']);
    }

    if ($_SESSION['settings']['area'] == 1) {
        echo "<br>\n";
        echo "Select default area:\n";
        echoAreaSelectSingel($usersettings['default_area']);
    }


    echo "<br><br>";

    echo "<div>Enable autosave when edit a session:";
    if ($usersettings['autosave'] == "1") {
        echo "<td><input type=\"checkbox\" name=\"autosave\" value=\"checked\" checked=\"checked\"></td>";
    } else {
        echo "<td><input type=\"checkbox\" name=\"autosave\" value=\"checked\"></td>";
    }
    //echo "<option value=\"team\">All sessions</option>\n";
    echo "</select>\n";
    echo "</div>";

    echo "<input type=\"submit\" value=\"Update\">";
    echo "</form>";
}

function echoChangeUserInfo($username)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $username = mysqli_real_escape_string($con,$username);

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   `members` ";
    $sqlSelect .= "WHERE  `username` = '$username' ";
    $sqlSelect .= "ORDER  BY `fullname` ASC ";

    $result = $dbm->executeQuery($con, $sqlSelect);

    $row = mysqli_fetch_array($result);
    $rowUserSettings = UserSettings::getUserSettings(false);
    echo "<h2>Edit user " . htmlspecialchars($row['fullname']) . "</h2>";
    echo "<form name=\"userinfo\" action=\"settings.php\" method=\"POST\">";
    echo " <input type=\"hidden\" name=\"usernametoupdate\" value=\"" . urlencode($username) . "\">\n";
    echo " <input type=\"hidden\" name=\"usersettings\" value=\"true\">\n";
    echo "    <input type=\"hidden\" name=\"command\" value= \"changeusersettings\">\n";
    echo "<table style=\"text-align: left; width: 1000px;\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
    echo "<tr>";
    echo "<td><b>Name</b></td>";
    echo "<td><b>User name</b></td>";
    echo "<td><b>Team name</b></td>";
    echo "<td><b>Active</b></td>";
    echo "<td><b>Admin</b></td>";
    echo "<td><b>Superuser</b></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>";
    HtmlFunctions::echoTeamSelect($rowUserSettings['teamname'], false);
    echo "</td>";
    if ($row['active'] == "1") {
        echo "<td><input type=\"checkbox\" name=\"active\" value=\"checked\" checked=\"checked\"></td>";
    } else {
        echo "<td><input type=\"checkbox\" name=\"active\" value=\"checked\"></td>";
    }
    if ($row['admin'] == "1") {
        echo "<td><input type=\"checkbox\" name=\"admin\" value=\"checked\" checked=\"checked\"></td>";
    } else {
        echo "<td><input type=\"checkbox\" name=\"admin\" value=\"checked\"></td>";
    }
    if ($row['superuser'] == "1") {
        echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"checked\" checked=\"checked\"></td>";
    } else {
        echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"checked\"></td>";
    }
    echo "</tr>";
    echo "</table>";
    echo "<input type=\"submit\" value=\"Update\">";
    echo "</form>";


    echoChangePassword($username);
}


function echoAllUsersInfo($removeDeletedUsers = false)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   `members` ";
    if ($removeDeletedUsers)
        $sqlSelect .= "WHERE deleted = false ";
    $sqlSelect .= "ORDER  BY `fullname` ASC ";


    $result = $dbm->executeQuery($con, $sqlSelect);

    echo "<h2>Users</h2>";

    echo "<table style=\"text-align: left; width: 1000px;\" border=\"1\"    cellpadding=\"0\" cellspacing=\"0\">";
    echo "<tr>";
    echo "<td><b>Name</b></td>";
    echo "<td><b>User name</b></td>";
    echo "<td><b>Active</b></td>";
    echo "<td><b>Admin</b></td>";
    echo "<td><b>Superuser</b></td>";
    echo "</tr>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td><a href=\"settings.php?user=" . urlencode($row['username']) . "&command=userinfo\">" . $row['fullname'] . "</a></td>";
        echo "<td>" . urldecode($row['username']) . "</td>";
        echo "<td>" . urldecode($row['active']) . "</td>";
        echo "<td>" . urldecode($row['admin']) . "</td>";
        echo "<td>" . urldecode($row['superuser']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";


}


function echoChangePassword($username)
{
    echo "<h2>Change Password</h2>\n";
    echo "<form name=\"password\" action=\"settings.php\" method=\"POST\">\n";
    echo "<table style=\"text-align: left; width: 1000px;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
    echo "    <input type=\"hidden\" name=\"usernametoupdate\" value=\"" . urlencode($username) . "\">\n";
    echo "    <input type=\"hidden\" name=\"command\" value= \"changepassword\">\n";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            New password\n";
    echo "        </td>\n";
    echo "        <td><input type=\"password\" size=\"50\" value=\"\" name=\"swpassword1\">\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            Retype password\n";
    echo "        </td>\n";
    echo "        <td><input type=\"password\" size=\"50\" value=\"\" name=\"swpassword2\">\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "        <td align=\"left\">\n";
    echo "            <input align=left type=\"submit\" value=\"Change password\" />\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>";
    echo "</form>\n";
}


function echoAddUser()
{
    echo "<h2>Add user:</h2>\n";
    echo "<table style=\"text-align: left; width: 800px;\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n";
    echo "            <form name=\"sprint\" action=\"settings.php\" method= \"POST\">\n";
    echo "                <input type=\"hidden\" name=\"command\" value= \"insertusertodb\">\n";
    echo "                <tr>\n";
    echo "                    <td width=150>\n";
    echo "                        Add User:\n";
    echo "                    </td>\n";
    echo "                    <td align=\"left\">\n";
    echo "                        Full Name<input type=\"text\" size=\"40\" value=\"\" name=\"fullname\">\n";
    echo "                    </td>\n";
    echo "                    <td align=\"left\">\n";
    echo "                        UserName<input type=\"text\" size=\"40\" value=\"\" name=\"username\">\n";
    echo "                    </td>\n";
    echo "                    <td align=\"left\">\n";
    echo "                        Password<input type=\"password\" size=\"40\" value=\"\" name=\"swpassword1\">\n";
    echo "                    </td>\n";
    echo "                    <td>Admin<input type=\"checkbox\" name=\"admin\" value=\"yes\"></td>\n";
    echo "                    <td>Superuser<input type=\"checkbox\" name=\"superuser\" value=\"yes\"></td>\n";
    echo "                    <td align=\"left\">\n";
    echo "                        <input align=left type=\"submit\" value=\"Add\" />\n";
    echo "                    </td>\n";
    echo "                </tr>\n";
    echo "            </form>\n";
    echo "        </table>\n";
}


function updateConfig()
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $normlizedsessiontime = 90;

    if (is_int((int)$_REQUEST["normlizedsessiontime"]) && (int)$_REQUEST["normlizedsessiontime"] != 0) {
        $normlizedsessiontime = $_REQUEST["normlizedsessiontime"];
    } else {
        echo "Normalized Sessions time is equal to 0 or not an integer, will use default value 90 min.<br>\n";
    }

    $team = 0;
    if (strcmp($_REQUEST["team"], "checked") == 0) {
        $team = 1;
    } else {
        $team = 0;
    }

    $sprint = 0;
    if (strcmp($_REQUEST["sprint"], "checked") == 0) {
        $sprint = 1;
    } else {
        $sprint = 0;
    }

    $teamsprint = 0;
    if (strcmp($_REQUEST["teamsprint"], "checked") == 0) {
        $teamsprint = 1;
    } else {
        $teamsprint = 0;
    }

    $area = 0;
    if (strcmp($_REQUEST["area"], "checked") == 0) {
        $area = 1;
    } else {
        $area = 0;
    }

    $env = 0;
    if (strcmp($_REQUEST["env"], "checked") == 0) {
        $env = 1;
    } else {
        $env = 0;
    }

    $publicview = 0;
    if (strcmp($_REQUEST["publicview"], "checked") == 0) {
        $publicview = 1;
    } else {
        $publicview = 0;
    }

    $wordcloud = 0;
    if (strcmp($_REQUEST["wordcloud"], "checked") == 0) {
        $wordcloud = 1;
    } else {
        $wordcloud = 0;
    }

    $url_to_dms = $_REQUEST["url_to_dms"];

    $url_to_rms = $_REQUEST["url_to_rms"];

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE settings ";
    $sqlUpdate .= "SET    `normalized_session_time` = $normlizedsessiontime, ";
    $sqlUpdate .= "       `team` = '$team', ";
    $sqlUpdate .= "       `sprint` = '$sprint', ";
    $sqlUpdate .= "       `area` = '$area', ";
    $sqlUpdate .= "       `url_to_dms` = '$url_to_dms', ";
    $sqlUpdate .= "       `url_to_rms` = '$url_to_rms', ";
    $sqlUpdate .= "       `testenvironment` = '$env', ";
    $sqlUpdate .= "       `publicview` = '$publicview', ";
    $sqlUpdate .= "       `teamsprint` = '$teamsprint', ";
    $sqlUpdate .= "       `wordcloud` = '$wordcloud' ";
    //$sqlUpdate .= "WHERE  `id` = '1'" ;

    $result = $dbm->executeQuery($con, $sqlUpdate);

    if (!$result) {
        echo "updateConfig: " . mysqli_error($con) . "<br/>";
    } else {
        echo "<br>Configuration changed.<br>\n";
    }

    $_SESSION['settings'] = getSessionWebSettings();


}

function updateUserPassword($username, $password1, $password2)
{

    if (strcmp($_SESSION['username'], $_REQUEST["usernametoupdate"]) == 0 || $_SESSION['useradmin'] == 1) {
        if (strcmp($password1, $password2) == 0) {


            $dbm = new dbHelper();
            $con = $dbm->connectToLocalDb();

            $password1 = stripslashes($password1);
            $username = mysqli_real_escape_string($con,$username);

            $md5password = md5($password1);
            //echo   $md5password;
            $username = urldecode($username);
            $sqlUpdate = "";
            $sqlUpdate .= "UPDATE `members` ";
            $sqlUpdate .= "SET    `password` ='$md5password' ";
            $sqlUpdate .= "WHERE  `members`.`username` = '$username' ";
            //echo $sqlUpdate;
            $result = $dbm->executeQuery($con, $sqlUpdate);

            if ($result) {
                echo "Password changed\n";
            } else {
                echo mysqli_error($con);
            }

        } else {
            echo "Passwords does not match, please try again.\n";
        }
    }
}

function updateUserSettingsForLoginUser()
{


    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE `user_settings` ";
    $sqlUpdate .= "SET    `list_view` ='" . mysqli_real_escape_string($con,$_REQUEST['listsettings']) . "' ,";
    if ($_REQUEST['team'] != '')
        $sqlUpdate .= "       `default_team` ='" . $_REQUEST['team'] . "' , ";
    else
        $sqlUpdate .= "       `default_team` =null , ";

    if ($_REQUEST['sprint'] != '')
        $sqlUpdate .= "       `default_sprint` ='" . $_REQUEST['sprint'] . "' , ";
    else
        $sqlUpdate .= "       `default_sprint` =null , ";

    if ($_REQUEST['teamsprint'] != '')
        $sqlUpdate .= "       `default_teamsprint` ='" . $_REQUEST['teamsprint'] . "' , ";
    else
        $sqlUpdate .= "       `default_teamsprint` =null , ";

    if ($_REQUEST['area'] != '')
        $sqlUpdate .= "       `default_area` ='" . $_REQUEST['area'] . "' , ";
    else
        $sqlUpdate .= "       `default_area` =null , ";

    if ($_REQUEST['autosave'] == 'checked')
        $sqlUpdate .= "       `autosave` ='1' ";
    else
        $sqlUpdate .= "       `autosave` ='0' ";
    $sqlUpdate .= "WHERE  `user_settings`.`username` = '" . $_SESSION['username'] . "' ";

    $result = $dbm->executeQuery($con, $sqlUpdate);

    if ($result) {
        echo "User settings changed\n";
    } else {
        echo mysqli_error($con);
    }

}


function createNewUser()
{
    $username = $_REQUEST["username"];
    $password = $_REQUEST["swpassword1"];
    $fullname = $_REQUEST["fullname"];
    $active = 1;
    $admin = 0;
    if (strcmp($_REQUEST["admin"], "yes") == 0) {
        $admin = 1;
    }

    $superuser = 0;
    if (strcmp($_REQUEST["superuser"], "yes") == 0) {
        $superuser = 1;
    }


    if ($username != "" && $password != "") {

        $activeToDb = 0;
        if ($active != "") {
            $activeToDb = 1;
        }

        $adminToDb = 0;
        if ($admin != "") {
            $adminToDb = 1;
        }

        $superuserToDb = 0;
        if ($superuser != "") {
            $superuserToDb = 1;
        }

        $md5password = md5($password);

        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();


        $username = mysqli_real_escape_string($con,$username);
        $fullname = mysqli_real_escape_string($con,$fullname);
        $activeToDb = mysqli_real_escape_string($con,$activeToDb);
        $adminToDb = mysqli_real_escape_string($con,$adminToDb);
        $superuserToDb = mysqli_real_escape_string($con,$superuserToDb);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO `members` ";
        $sqlInsert .= "            (`username`, ";
        $sqlInsert .= "             `password`, ";
        $sqlInsert .= "             `fullname`, ";
        $sqlInsert .= "             `active`, ";
        $sqlInsert .= "             `admin`, ";
        $sqlInsert .= "             `superuser`) ";
        $sqlInsert .= "VALUES      ('$username', ";
        $sqlInsert .= "             '$md5password', ";
        $sqlInsert .= "             '$fullname', ";
        $sqlInsert .= "             '$activeToDb', ";
        $sqlInsert .= "             '$adminToDb', ";
        $sqlInsert .= "             '$superuserToDb')";

        $result = $dbm->executeQuery($con, $sqlInsert);

        if ($result) {
            echo "<div>User added</div>\n";
        } else {
            echo mysqli_error($con);
        }

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO `user_settings` ";
        $sqlInsert .= "            (`username`, ";
        $sqlInsert .= "             `list_view`) ";
        $sqlInsert .= "VALUES      ('$username', ";
        $sqlInsert .= "             'all')";

        $result = $dbm->executeQuery($con, $sqlInsert);

        if ($result) {
            echo "<div>User settings added</div>\n";
        } else {
            echo mysqli_error($con);
        }


    } else {
        echo "Please try again, username and password is mandatory\n";
    }
}

function updateUserSettings($userToChange, $active, $admin, $superuser, $team)
{
    $activeToDb = 0;
    if ($active != "") {
        $activeToDb = 1;
    }

    $adminToDb = 0;
    if ($admin != "") {
        $adminToDb = 1;
    }

    $superuserToDb = 0;
    if ($superuser != "") {
        $superuserToDb = 1;
    }

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $activeToDb = mysqli_real_escape_string($con,$activeToDb);
    $adminToDb = mysqli_real_escape_string($con,$adminToDb);
    $superuserToDb = mysqli_real_escape_string($con,$superuserToDb);
    $userToChange = mysqli_real_escape_string($con,urldecode($userToChange));

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE `members` ";
    $sqlUpdate .= "SET    `active` = '$activeToDb', ";
    $sqlUpdate .= "       `admin` = '$adminToDb', ";
    $sqlUpdate .= "       `superuser` = '$superuserToDb' ";
    $sqlUpdate .= "WHERE  `username` = '$userToChange'";

    $result = $dbm->executeQuery($con, $sqlUpdate);


    if ($result) {
        echo "User settings changed\n";
    } else {
        echo mysqli_error($con);
    }

    $sqlUpdate = "UPDATE user_settings SET teamname='$team' WHERE username='$userToChange'";
    //echo $sqlUpdate;
    $result = $dbm->executeQuery($con, $sqlUpdate);

    if ($result) {

    } else {
        echo mysqli_error($con);
    }


}


?>