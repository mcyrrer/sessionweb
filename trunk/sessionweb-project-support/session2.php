<?php
session_start();
//Check that you are logged in as a user
require_once('include/validatesession.inc');
require_once('classes/dbHelper.php');
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


$s2 = new session2();

$s2->showHtml();


require_once("include/footer.php.inc");

class session2
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

        echo ' <div id="divTitle"><label for="input_title">Session title:</label>
              <input type="text" id="input_title" size="130" style="border: 0; font-weight: bold; border-color: #808500" /></div>';
        echo '
        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">Setup</a></li>
            <li><a href="#tabs-2">Charter</a></li>
            <li><a href="#tabs-3">Notes</a></li>
            <li><a href="#tabs-4">Metrics</a></li>
          </ul>
          <div id="tabs-1">';
        echo '<table class="sTable"><tr><td>';
        echo "<h3>Sprint:</h3>  <p>" . $this->formHelper->getSprintSelect() . "</p>";
        echo "<h3>Team:</h3><p>" . $this->formHelper->getTeamSelect() . "</p>";
        echo "<h3>Additional tester:</h3><p>" . $this->formHelper->AdditionalTester() . "</p>";

        echo "<h3>Area:</h3><p>" . $this->formHelper->getAreaSelect() . "</p>";

        echo "<h3>Testenvironment:</h3><p>" . $this->formHelper->getEnvironmentSelect() . "</p>";

        echo "<h3>Software under test:</h3>";
        echo "<textarea rows='4' cols='50' id='idSoftwareUnderTest' name='nameSoftwareUnderTest'></textarea>";
        echo '</td><td>';

        echo '<span class="sH3">Test requirements:</span>';
        echo '<img id="addReq"  src="pictures/add.png" alt=""><input type="text" class="sInput" id="new_requirement" size="10" ><br>';
        echo "<span id='testReqId'></span></p>";
        echo '<span class="sH3">Link to other sessions::</span>';
        echo '<img id="addSessionLink"  src="pictures/add.png" alt=""><input type="text" class="sInput" id="new_sessionlink" size="10" ><br>';
        echo "<span id='linkToOtherSessions'></span></p>";
        echo "<h3>Automatically fetched software versions:</h3>";
        echo "<span id='autoSoftwareVersions'></span></p>";
        echo '</td></tr></table>';
        echo '</div>

            <div id="tabs-2">';

        echo '<div id="idcharter">Charter<br><textarea class="ckeditor" name="chartereditor"></textarea></div>';

        echo '</div>
            <div id="tabs-3">';
        echo '<div id="idnotes">Notes<br><textarea class="ckeditor" name="noteseditor" rows="600"></textarea></div>';

        echo '</div>
            <div id="tabs-4">';
        echo "<br>Attachments:<br>";
        echo "Defects:<br>";
        echo "Metrics:<br>";
        echo '
              <label for="amount">Setup time:</label>

              <label for="amount">Test time:</label>


              <label for="amount">Bug time:</label>


              <label for="amount">Opportunity time:</label>';
;
        echo "Session mood:<br>";
        echo "Executed:<br>";
        echo '</div>
</div>';
    }
}

?>