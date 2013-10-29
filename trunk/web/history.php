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
    private $dh;

    function __construct()
    {
        $this->logger = new logging();
        $this->formHelper = new formHelper();
        $this->session = new sessionObject($_REQUEST['sessionid']);
        $this->sessionHelper = new sessionHelper();
        $this->quaryHelper = new QueryHelper();
        $this->dh = new dbHelper();
    }

    public function showHtml()
    {
        if ($this->sessionHelper->isUserAllowedToEditSession($this->session)) {
            $this->showHtmlAllowedToViewHistory();
        } else {
            echo "User not allowed to view history, only the owner of the session can do that.";
        }
    }

    private function showHtmlAllowedToViewHistory()
    {
        $con = $this->dh->db_getMySqliConnection();

        echo '<div>';
        echo '<h1>History</h1>';
        echo '<div>This page shows history of the changes made to charter and notes content for charter '.$this->session->getTitle().'</div>';
        echo '<table>';
        echo '<tr>';
        echo '<td valign="top" width="400">';
        echo '<h3>Saved versions of charter content</h3>';
        $sqlGetCharterHistory="SELECT * FROM mission_incremental_save WHERE versionid=".$this->session->getVersionid()." and charter not like '' ORDER BY id DESC";
        $charterHistoryResult = dbHelper::sw_mysqli_execute($con, $sqlGetCharterHistory, __FILE__, __LINE__);
        /* fetch object array */
        while ($row = $charterHistoryResult->fetch_array(MYSQLI_ASSOC)) {
            echo '<span class="charterhistory"><a href="historyiframe.php?sessionid='.$this->session->getSessionid().'&id='.$row['id'].'" target="_blank">'. $row['timestamp_saved'].'</a></span><br>';
        }
        echo '</td>';
        echo '<td valign="top">';
        echo '<h3>Saved versions of notes content</h3>';
        $sqlGetCharterHistory="SELECT * FROM mission_incremental_save WHERE versionid=".$this->session->getVersionid()." and notes not like '' ORDER BY id DESC";
        $charterHistoryResult = dbHelper::sw_mysqli_execute($con, $sqlGetCharterHistory, __FILE__, __LINE__);
        /* fetch object array */
        while ($row = $charterHistoryResult->fetch_array(MYSQLI_ASSOC)) {
            echo '<span class="charterhistory"><a href="historyiframe.php?sessionid='.$this->session->getSessionid().'&id='.$row['id'].'" target="_blank">'. $row['timestamp_saved'].'</a></span><br>';
        }
        echo '</td>';
        echo '</tr>';
        echo '</table   >';


        /* free result set */
        $charterHistoryResult->close();

        echo '</div>';
    }

}

?>