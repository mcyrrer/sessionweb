<?php
session_start();
//Check that you are logged in as a user
require_once('include/validatesession.inc');
require_once('classes/dbHelper.php');
require_once('classes/QueryHelper.php');
require_once('classes/formHelper.php');
require_once('classes/sessionHelper.php');
require_once('classes/logging.php');
require_once('classes/sessionObject.php');
require_once('config/db.php.inc');
require_once ('classes/pagetimer.php');
if (is_file("include/customfunctions.php.inc")) {
    include "include/customfunctions.php.inc";
}


//Create a new session and forward the browser to it
if (!isset($_REQUEST['sessionid'])) {
    $logger = new logging();
    $session = new sessionObject();
    $sessionid = $session->getSessionid();
    if(isset($_REQUEST['requirement']))
    {
        $reqArray[]=$_REQUEST['requirement'];
        $session->setRequirements($reqArray);
    }
    $session->saveObjectToDb();
    header("Location: edit.php?sessionid=$sessionid");
    exit();
}


require_once("include/header.php.inc");
echo "<div id='message'></div>";


$s2 = new Edit();

$s2->showHtml();


require_once("include/footer.php.inc");

class Edit
{
    private $logger;
    private $formHelper;
    private $session;
    private $sessionHelper;
    private $quaryHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->formHelper = new formHelper();
        $this->session = new sessionObject($_REQUEST['sessionid']);
        $this->sessionHelper = new sessionHelper();
        $this->quaryHelper = new QueryHelper();
    }

    public function showHtml()
    {
        if ($this->sessionHelper->isUserAllowedToEditSession($this->session)) {
            $this->showHtmlAllowedToEditSession();
        } else {
            echo "User not allowed to edit session, please ask owner of session to reassign it and try again.";
        }
    }

    private function showHtmlAllowedToEditSession()
    {
        echo '<div id=ui-master>';
        echo ' <div id="divTitle" >

              <label for="input_title" >Session title:</label>
              <input type="text" id="input_title" size="80" class="sInput" title="Company name or ticker">';
        echo '<button id="setExecuted">Mark as executed</button><button id="unsetExecuted">Mark as in progress</button>';
        echo '</div>';
        echo '
        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">Setup</a></li>
            <li><a href="#tabs-2">Charter</a></li>
            <li><a href="#tabs-3">Notes</a></li>
            <li><a href="#tabs-4">Metrics</a></li>
            <li><a href="#tabs-5">Attachments</a></li>
            </ul>
          <div id="tabs-1">';
        echo '<table class="sTable"><tr><td>';
        echo "<p id='choose_user_config'><img src='pictures/notify-star.png'> Insert default team, sprint and area</p>";
        if ($_SESSION['settings']['sprint'] == 1) {
            echo "<span class='sH3'>Sprint:</span>  <p>" . $this->formHelper->getSprintSelect() . "<span class='minmax' id='minimizeSprint'>[&uarr;]</span><span class='minmax' id='maximizeSprint'>[&darr;]</span></p>";
        }
        if ($_SESSION['settings']['team'] == 1) {
            echo "<span class='sH3'>Team:</span><p>" . $this->formHelper->getTeamSelect() . "<span class='minmax' id='minimizeTeam'>[&uarr;]</span><span class='minmax' id='maximizeTeam'>[&darr;]</span></p>";
        }
        echo "<span class='sH3'>Additional tester:</span><p>" . $this->formHelper->AdditionalTester() . "<span class='minmax' id='minimizeAddTest'>[&uarr;]</span><span class='minmax' id='maximizeAddTest'>[&darr;]</span></p>";
        if ($_SESSION['settings']['area'] == 1) {

            echo '<span class="sH3">Area:</span><span id="addNewArea">[+]</span><input type="text" class="sInput" id="addNewAreaInput" size="10" ><p>' . $this->formHelper->getAreaSelect() . "<span class='minmax' id='minimizeArea'>[&uarr;]</span><span class='minmax' id='maximizeArea'>[&darr;]</span></p>";
        }

        if ($_SESSION['settings']['testenvironment'] == 1) {
            echo "<span class='sH3'>Testenvironment:</span><p>" . $this->formHelper->getEnvironmentSelect() . "<span class='minmax' id='minimizeTestenv'>[&uarr;]</span><span class='minmax' id='maximizeTestenv'>[&darr;]</span></p>";
        }
        if ($_SESSION['settings']['custom1'] == 1) {
            echo "<span class='sH3'>" . $_SESSION['settings']['custom1_name'] . ":</span><p>" . $this->formHelper->getCustomFieldSelect(null, "custom1", $_SESSION['settings']['custom1_multiselect']) . "<span class='minmax' id='minimizeCust1'>[&uarr;]</span><span class='minmax' id='maximizeCust1'>[&darr;]</span></p>";
        }
        if ($_SESSION['settings']['custom2'] == 1) {
            echo "<span class='sH3'>" . $_SESSION['settings']['custom2_name'] . ":</span><p>" . $this->formHelper->getCustomFieldSelect(null, "custom2", $_SESSION['settings']['custom2_multiselect']) . "<span class='minmax' id='minimizeCust2'>[&uarr;]</span><span class='minmax' id='maximizeCust2'>[&darr;]</span></p>";
        }
        if ($_SESSION['settings']['custom3'] == 1) {
            echo "<span class='sH3'>" . $_SESSION['settings']['custom3_name'] . ":</span><p>" . $this->formHelper->getCustomFieldSelect(null, "custom3", $_SESSION['settings']['custom3_multiselect']) . "<span class='minmax' id='minimizeCust3'>[&uarr;]</span><span class='minmax' id='maximizeCust3'>[&darr;]</span></p>";
        }
        echo "<span class='sH3'>Software under test:</span><br>";
        echo "<textarea rows='4' cols='50' id='idSoftwareUnderTest' name='nameSoftwareUnderTest' class='fixedWidth'></textarea>";
        echo '</td><td>';

        echo "<div class='itemList'>";
        echo '<span class="sH3">Test requirements:</span>';
        echo '<span id="addReq">[+]</span><input type="text" class="sInput" id="new_requirement" size="10" ><br>';
        echo "<span id='testReqId'></span>";
        echo "</div>";

        echo "<div class='itemList'>";
        echo '<span class="sH3">Bug reported:</span>';
        echo '<span id="addBug">[+]</span><input type="text" class="sInput" id="new_bug" size="10" ><br>';
        echo "<span id='testBugId'></span>";
        echo "</div>";

        echo "<div class='itemList'>";
        echo '<span class="sH3">Link to other sessions:</span>';
        echo '<span id="addSessionLink">[+]</span><input type="text" class="sInput" id="new_sessionlink" size="10" ><br>';
        echo "<span id='linkToOtherSessions'></span>";
        echo "</div>";

        if ($this->quaryHelper->isTestEnvoronmentUrlDefined()) {
            echo "<div class='itemList'>";
            echo "<span class='sH3'>Automatically fetched software versions:</span>";
            echo '<span id="addAutoFetchedSw">[+]</span><br>';
            echo "<span id='autoSoftwareVersions'></span>";
            echo "</div>";
        }
        if ($_SESSION['settings']['wisemapping'] == 1) {
            echo "<div class='itemList'>";
            echo "<span class='sH3'>Mindmaps:</span>";
            echo '<span id="addMindMap">[+]</span><br>';
            echo "<span id='mindMaps'></span>";
            echo "</div>";
        }


        echo '</td></tr></table>';
        echo '</div>

            <div id="tabs-2">';

        echo '<div id="idcharter"><span class="larger">Charter</span> <span id="charterStatus"></span><br><textarea name="chartereditor" rows="30" cols="30">&nbsp;</textarea></div>';

        echo '</div>
            <div id="tabs-3">';
        echo '<div id="idnotes"><span class="larger">Notes</span><span id="notesStatus"></span>&nbsp;&nbsp;&nbsp;&nbsp;<img id="reportBug" src="pictures/bug.png" alt="Report a Bug"><input type="text" class="sInput" id="new_bug2" size="10" title="Add bug id and press enter"><textarea name="noteseditor" rows="30" cols="30">&nbsp;d</textarea></div>';

        echo '</div>
            <div id="tabs-4">';

        echo '<div><span class="larger">Metrics</span>:';
        echo "<span id='metricsCalc'></span><br>";

        echo "Setup(%):";
        $this->sessionHelper->echoPercentSelection("setupId", "metrics", "setup");
        echo "Test(%):";
        $this->sessionHelper->echoPercentSelection("testId", "metrics", "test");
        echo "Bug(%):";
        $this->sessionHelper->echoPercentSelection("bugId", "metrics", "bug");
        echo "Opportunity(%):";
        $this->sessionHelper->echoPercentSelection("oppId", "metrics", "opportunity");
        echo "Session duration (min):";
        $this->sessionHelper->echoDurationSelection("durId", "duration", "duration");
        echo "</div>";
        echo "<br><br><div>Session mood:";
        echo '<div>
            <img class="session_mode" id="sm_1" src="pictures/emotes/face-cool.png" alt="1">
            <img class="session_mode" id="sm_2" src="pictures/emotes/face-plain.png" alt="2">
            <img class="session_mode" id="sm_3" src="pictures/emotes/face-sad.png" alt="3">
            <img class="session_mode" id="sm_4" src="pictures/emotes/face-angry.png" alt="4">
            </div></div>';


        echo '</div>
            <div id="tabs-5">';
        $sessionId = $this->session->getSessionid();
        echo '<span class="larger">Attachments</span>';
        echo '<iframe src="include/jQuery-File-Upload/index.php?sessionid=' . $sessionId . '" width="100%" height="600" frameborder="0"></iframe>';
        echo '</div></div> ';
        echo '</div>';
        echo '<div id="inc_saved_versions"><a href="history.php?sessionid='.$this->session->getSessionid().'" target="_blank">Show older versions of charter and notes content</a></div>';
    }
}

?>