<?php

require_once('classes/autoloader.php');
require_once('include/apistatuscodes.inc');

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
    private $dbm;

    function __construct()
    {
        $sessionIdAsCasted2Int = intval($_REQUEST['sessionid']);
        if (isset($_REQUEST['sessionid']) && $_REQUEST['sessionid'] != null && is_int($sessionIdAsCasted2Int) && $sessionIdAsCasted2Int != 0) {
            $this->logger = new logging();
            $this->formHelper = new formHelper();
            $this->session = new sessionObject($_REQUEST['sessionid']);
            $this->sessionHelper = new sessionHelper();
            $this->quaryHelper = new QueryHelper();
            $this->dbm = new dbHelper();
        } else {
            echo "Please add sessionid parameter (or a correct one...) since it is needed";
            exit();
        }
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
        $con = $this->dbm->connectToLocalDb();

        echo '<div>';
        echo '<h1>History</h1>';
        echo '<div>This page shows history of the changes made to charter and notes content for charter ' . $this->session->getTitle() . '</div>';
        echo '<table>';
        echo '<tr>';
        echo '<td valign="top" width="400">';
        echo '<h3>Saved versions of charter content</h3>';
        $sqlGetCharterHistory = "SELECT * FROM mission_incremental_save WHERE versionid=" . $this->session->getVersionid() . " and charter not like '' ORDER BY id DESC";
        $charterHistoryResult = $this->dbm->executeQuery($con, $sqlGetCharterHistory);

        /* fetch object array */
        if ($charterHistoryResult) {
            while ($row = $charterHistoryResult->fetch_array(MYSQLI_ASSOC)) {
                echo '<span class="charterhistory"><a href="historyiframe.php?sessionid=' . $this->session->getSessionid() . '&id=' . $row['id'] . '" target="_blank">' . $row['timestamp_saved'] . '</a></span><br>';
            }
            $charterHistoryResult->close();

        }
        echo '</td>';
        echo '<td valign="top">';
        echo '<h3>Saved versions of notes content</h3>';
        $sqlGetCharterHistory = "SELECT * FROM mission_incremental_save WHERE versionid=" . $this->session->getVersionid() . " and notes not like '' ORDER BY id DESC";
        $charterHistoryResult = $this->dbm->executeQuery($con, $sqlGetCharterHistory);

        /* fetch object array */
        if ($charterHistoryResult) {
            while ($row = $charterHistoryResult->fetch_array(MYSQLI_ASSOC)) {
                echo '<span class="charterhistory"><a href="historyiframe.php?sessionid=' . $this->session->getSessionid() . '&id=' . $row['id'] . '" target="_blank">' . $row['timestamp_saved'] . '</a></span><br>';
            }
            $charterHistoryResult->close();

        }
        echo '</td>';
        echo '</tr>';
        echo '</table   >';


        /* free result set */

        echo '</div>';
    }

}

?>