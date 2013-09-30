<?php
session_start();
//Check that you are logged in as a user
require_once('include/validatesession.inc');
require_once('classes/AccessManagement.php');
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


require_once("include/header.php.inc");
echo "<div id='message'></div>";


$s2 = new View();

$s2->showHtml();


require_once("include/footer.php.inc");

class View
{
    private $logger;
    private $formHelper;
    private $session;
    private $sessionHelper;

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
        if (isset($_REQUEST['debrief'])) {
            if ($this->sessionHelper->isUserAllowedToDebriefSession($this->session)) {
                $this->showHtmlAllowedToViewDebriefSession();
            } else {
                echo "You are not allowed to debrief session, only superuser or admin can do a debrief.";
            }
        } else {
            $this->showHtmlAllowedToViewDebriefSession();
        }
    }

    private function showHtmlAllowedToViewDebriefSession()
    {
        echo '<div id=ui-mastnoteseditorer>'; #fff
        echo ' <div id="divTitle" ><label for="input_title">Session title:</label>
              <span type="text" id="input_title" size="80" class="sInput"> </span>';
        echo '</div>';
        echo '
        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">Setup</a></li>
            <li><a href="#tabs-2">Charter</a></li>
            <li><a href="#tabs-3">Notes</a></li>
            <li><a href="#tabs-4">Metrics</a></li>
            <li><a href="#tabs-5">Attachments</a></li>';
        if (isset($_REQUEST['debrief']) || $this->session->isDebriefed()) {
            echo             '<li><a href="#tabs-6">Debrief</a></li>';
        }

        echo '</ul>
          <div id="tabs-1">';
        echo '<table class="sTable"><tr><td class="fixedWidth">';
        if ($_SESSION['settings']['sprint'] == 1) {
            echo "<span class='sH3'>Sprint:</span>  <p><span id='idSprint'></span></p>";
        }
        if ($_SESSION['settings']['team'] == 1) {
            echo "<span class='sH3'>Team:</span><p><span id='idTeam'></span></p>";
        }
        echo '<span class="sH3">Additional tester:</span></span><p><span id="idAdditionalTester"></span></p>';
        if ($_SESSION['settings']['area'] == 1) {
            echo '<span class="sH3">Area:</span></span><p><span id="idArea"></span></p>';
        }
        if ($_SESSION['settings']['testenvironment'] == 1) {
            echo '<span class="sH3">Testenvironment:</span></span><p><span id="idEnvironment"></span></p>';
        }


        if ($_SESSION['settings']['custom1'] == 1) {
            echo '<span class="sH3">' . $_SESSION['settings']['custom1_name'] . ':</span></span><p><span id="custom1"></span></p>';
        }
        if ($_SESSION['settings']['custom2'] == 1) {
            echo '<span class="sH3">' . $_SESSION['settings']['custom2_name'] . ':</span></span><p><span id="custom2"></span></p>';
        }
        if ($_SESSION['settings']['custom3'] == 1) {
            echo '<span class="sH3">' . $_SESSION['settings']['custom3_name'] . ':</span></span><p><span id="custom3"></span></p>';
        }
        echo '<span class="sH3">Software under test:</span></span><p><span id="idSoftwareUnderTest"></span></p>';

        echo '</td><td>';
        echo "<div class='itemList' class='fixedWidth'>";
        echo '<span class="sH3">Test requirements:</span><br>';
        echo "<span id='testReqId'></span>";
        echo "</div>";

        echo "<div class='itemList'>";
        echo '<span class="sH3">Bug reported:</span><br>';
        echo "<span id='testBugId'></span>";
        echo "</div>";

        echo "<div class='itemList'>";
        echo '<span class="sH3">Link to other sessions:</span><br>';
        echo "<span id='linkToOtherSessions'></span>";
        echo "</div>";

        if ($this->quaryHelper->isTestEnvoronmentUrlDefined()) {
            echo "<div class='itemList'>";
            echo "<span class='sH3'>Automatically fetched software versions:</span><br>";
            echo "<span id='autoSoftwareVersions'></span>";
            echo "</div>";
        }

        if ($_SESSION['settings']['wisemapping'] == 1) {
            echo "<div class='itemList'>";
            echo "<span class='sH3'>Mindmaps:</span><br>";
            echo "<span id='mindMaps'></span>";
        }
        echo "</div>";


        echo '</td></tr></table>';
        echo '</div>

            <div id="tabs-2">';

        echo '<div id="idcharter"><span class="larger">Charter</span> <span id="charterStatus"></span><br><div id="chartereditor"></div></div>';

        echo '</div>
            <div id="tabs-3">';
        echo '<table><tr><td width="50%">';
        echo '<div id="idnotes">
                    <span class="larger">Notes</span>
                    <span id="notesStatus"></span>
                    <div id="noteseditor"></div>
              </div></td>';
//        if (AccessManagement::isCurrentUserAllowedToDebiref() && isset($_REQUEST['debrief'])) {
//
//            echo '<td>  <div id="debrief">';
//            echo '      <div id="iddebrief">
//                            <span class="larger">Debrief</span>&nbsp;&nbsp;
//                            <span id="debriefStatus"></span>
//                            <div id="debriefNotes">
//                                <input class="dbStatus" id="notdebriefed" type="radio" name="debriefstatus" value="notdebriefed" >Not debriefed
//                                |
//                                <input class="dbStatus" id="debriefed" type="radio" name="debriefstatus" value="debriefed">Debriefed
//                                |
//                                <input class="dbStatus" id="closed" type="radio" name="debriefstatus" value="closed">Closed
//                            </div>
//                            <textarea name="debriefeditor" rows="30" cols="30">&nbsp;d</textarea>
//                        </div>';
//            echo '  </div></td>';
//        } elseif ($this->session->isDebriefed()) {
//            echo '<td>      <div id="debrief">';
//            echo '          <div id="iddebrief">
//                                <span class="larger">Debrief</span>&nbsp;&nbsp;
//                            <div id="debriefStatus"></div>
//                            <div id="debriefText"></div>
//                        </div></td>';
//        } else {
//            $this->logger->info("Tried to debrief but is not authorized",__FILE__,__LINE__);
//        }
        echo '</tr></table>';
        echo '</div>
            <div id="tabs-4">';

        echo '<div><span class="larger">Metrics:</span>';
        echo "<div id='metricsPic'></div><br>";
        echo "<div>Duration: <span id='durId'></span> min</div><br>";

        echo "</div>";
        echo "<br><br><div>Session mood:";
        echo '<div>
            <img class="session_mode" id="sm_1" src="pictures/emotes/face-cool.png" alt="1">
            <img class="session_mode" id="sm_2" src="pictures/emotes/face-plain.png" alt="2">
            <img class="session_mode" id="sm_3" src="pictures/emotes/face-sad.png" alt="3">
            <img class="session_mode" id="sm_4" src="pictures/emotes/face-angry.png" alt="4">
            </div></div>';


        echo '</div>';
        echo '<div id="tabs-5">';
        echo '  <div id="attachments">';
        echo '  <span class="larger">Attachments</span>';
        echo '  </div>';
        echo '</div>';
        if (AccessManagement::isCurrentUserAllowedToDebiref() && isset($_REQUEST['debrief'])) {

            echo '<div id="tabs-6">';
            echo '  <div id="debrief">';
            echo '      <div id="iddebrief">
                            <span class="larger">Debrief</span>&nbsp;&nbsp;
                            <span id="debriefStatus"></span>
                            <div id="debriefNotes">
                                <input class="dbStatus" id="notdebriefed" type="radio" name="debriefstatus" value="notdebriefed" >Not debriefed
                                |
                                <input class="dbStatus" id="debriefed" type="radio" name="debriefstatus" value="debriefed">Debriefed
                                |
                                <input class="dbStatus" id="closed" type="radio" name="debriefstatus" value="closed">Closed
                            </div>
                            <textarea name="debriefeditor" rows="30" cols="30">&nbsp;d</textarea>
                        </div>';
            echo '  </div>';
            echo '</div>';
        } elseif ($this->session->isDebriefed()) {
            echo '<div id="tabs-6">';
            echo '      <div id="debrief">';
            echo '          <div id="iddebrief">
                                <span class="larger">Debrief</span>&nbsp;&nbsp;
                            <div id="debriefStatus"></div>
                            <div id="debriefText"></div>
                        </div>';
            echo '</div>';
        } else {
            $this->logger->info("Tried to debrief but is not authorized",__FILE__,__LINE__);
        }


        echo '</div></div> ';
//        echo '</div>';
    }
}

?>