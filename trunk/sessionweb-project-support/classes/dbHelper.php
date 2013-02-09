<?php
$rootPath = checkIfRootFolder("");
require_once 'logging.php';
require_once 'pagetimer.php';
require_once $rootPath.'/config/db.php.inc';

/**
 * Class to help out with common mysql tasks
 */
class dbHelper
{
    private $logger;

    function __construct()
    {
        $this->logger = new logging();
    }

    public function db_getMySqliConnection()
    {
        $con = mysqli_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB,DB_NAME_SESSIONWEB) or die("cannot connect");
        mysqli_select_db($con,DB_NAME_SESSIONWEB)or die("cannot select DB");
        mysqli_set_charset($con,'utf8');
        return $con;
    }

    static function sw_mysql_execute($query, $file = "", $line = "")
    {
        $logger = new logging();
        $pageTimer = new pagetimer();
        $pageTimer->startMeasurePageLoadTime();
        $result = mysql_query($query);
        $pageTimer->stopMeasurePageLoadTime();

        if (strlen($query) > 100) {
            $queryToLog = substr($query,0, 99) . ".... Execution time: ".$pageTimer->getTime();
        } else {

            echo $query;
           $queryToLog = $query . ". Execution time: ".$pageTimer->getTime();
        }

        $logger->timer($queryToLog, $file, $line);
        $logger->sql($query, $file, $line);
        return $result;
    }

    static function sw_mysqli_execute($con, $query, $file = "", $line = "")
    {
        $logger = new logging();
        $pageTimer = new pagetimer();
        $pageTimer->startMeasurePageLoadTime();
        $result = mysqli_query($con, $query);
        $pageTimer->stopMeasurePageLoadTimeWithoutLog();
        if (strlen($query) > 100) {
            $queryToLog = substr($query,0, 99) . ".... Execution time: ".$pageTimer->getTime();
        } else {
            $queryToLog = $query . ". Execution time: ".$pageTimer->getTime();
        }
        $logger->timer($queryToLog, $file, $line);
        $logger->sql($query, $file, $line);
        return $result;
    }

    static function escape($con,$toEscape)
    {
        return mysqli_real_escape_string($con,$toEscape);
    }
}

?>