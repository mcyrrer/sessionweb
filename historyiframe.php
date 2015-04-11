<?php
require_once('classes/autoloader.php');
require_once('include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

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
        $this->logger = new logging();
        $this->formHelper = new formHelper();
        $this->session = new sessionObject($_REQUEST['sessionid']);
        $this->sessionHelper = new sessionHelper();
        $this->quaryHelper = new QueryHelper();
        $this->dbm = new dbHelper();
    }

    public function showHtml()
    {
        if ($this->sessionHelper->isUserAllowedToEditSession($this->session)) {
            $this->showHtmlAllowedViewHistoryIframe();
        } else {
            echo "User not allowed to view history, only the owner of the session can do that.";
        }
    }

    private function showHtmlAllowedViewHistoryIframe()
    {
        $con = $this->dbm->connectToLocalDb();

        $id = $_REQUEST['id'];
        $id = $this->dbm->escape($con, $id);
        $sqlGetCharterHistory = "SELECT * FROM mission_incremental_save WHERE id=" . $id;
        $charterHistoryResult = $this->dbm->executeQuery($con, $sqlGetCharterHistory, __FILE__, __LINE__);

        echo '<h2>This page shows an older version of charter text or notes text for charter title<br>' . $this->session->getTitle() . '</h2>';
        while ($row = $charterHistoryResult->fetch_array(MYSQLI_ASSOC)) {
            echo "Saved at " . $row['timestamp_saved'] . '<br>';
            echo $row['charter'];
            echo $row['notes'];

        }


        /* free result set */
        $charterHistoryResult->close();

    }

}

?>