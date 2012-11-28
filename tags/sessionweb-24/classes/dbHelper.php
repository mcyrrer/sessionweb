<?php
require_once 'logging.php';
require_once 'pagetimer.php';

/**
 * Class to help out with common mysql tasks
 */
class dbHelper
{
    private $logger;

    function __construct($sessionid = null)
    {
        $this->logger = new logging();
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
}

?>