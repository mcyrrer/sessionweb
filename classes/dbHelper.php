<?php
//require_once 'PathHelper.php';
//require_once 'logging.php';
//require_once 'pagetimer.php';
//$rootPath = PathHelper::getRootPath_v2();
//
//require_once $rootPath . '/config/db.php.inc';

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

    function connectToLocalDb()
    {
           if (isset($_SESSION['mysqliCon'])
               && mysqli_ping($_SESSION['mysqliCon'])) {
                return $_SESSION['mysqliCon'];
            } else {
                $orgErrorLevel = error_reporting();
                error_reporting(0);
                if (!isset($_SESSION['localdbhost'])) {
                    $_SESSION['localdbhost'] = "DB_1";
                    $this->logger->debug("SESSION info about localdbhost not set will set db to DB_1", __FILE__, __LINE__);
                }

                if (isset($_SESSION['localdbhost']) && strcmp("DB_1", $_SESSION['localdbhost']) == 0) {
                    $this->logger->debug("DB " . "DB_1" . " is active will try to connect to it", __FILE__, __LINE__);
                    $con = $this->connectToDb(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB, DB_NAME_SESSIONWEB);
                } else {
                    $this->logger->debug("DB " . "DB_2" . " is active will try to connect to it", __FILE__, __LINE__);
                    $con = $this->connectToDb(DB_HOST_SESSIONWEB_2, DB_USER_SESSIONWEB_2, DB_PASS_SESSIONWEB_2, DB_NAME_SESSIONWEB_2);
                }
                if ($con == false) {
                    $this->logger->warn("Could not connect to database " . $_SESSION['localdbhost'], __FILE__, __LINE__);
                    if (strcmp("DB_1", $_SESSION['localdbhost'])==0) {
                       $this->logger->info("2:nd choice DB at " . DB_HOST_SESSIONWEB_2 . ": will try to connect to it", __FILE__, __LINE__);
                        $con = $this->connectToDb(DB_HOST_SESSIONWEB_2, DB_USER_SESSIONWEB_2, DB_PASS_SESSIONWEB_2, DB_NAME_SESSIONWEB_2);
                        $_SESSION['localdbhost'] = "DB_2";

                    } else {
                        $this->logger->info("1:nd choice DB at " . DB_USER_SESSIONWEB . ": will try to connect to it", __FILE__, __LINE__);
                        $con = $this->connectToDb(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB, DB_NAME_SESSIONWEB);
                        $_SESSION['localdbhost'] = "DB_1";
                    }
                }
                if ($con == null) {
                    $this->logger->error("Could not connect to DB_1 or DB_2.. will die ", __FILE__, __LINE__);
                    die("Could not connect to application database");
                } else {
                    $this->logger->info("Mysqli application db: Connected to " . mysqli_get_host_info($con), __FILE__, __LINE__);

                }
                $this->logger->debug("Connected to db", __FILE__, __LINE__);
                error_reporting($orgErrorLevel);

                $_SESSION['mysqliCon'] = $con;
                mysqli_set_charset($con, 'utf8');
                return $con;
            }
    }


    private function connectToDb($host, $user, $password, $port = 3306)
    {
        return mysqli_connect($host, $user, $password, $port);
    }


    function executeQuery($con, $sql)
    {
        $callingInfo[0] = debug_backtrace()[0]['file'];
        $callingInfo[1] = debug_backtrace()[0]['line'];
        
        mysqli_set_charset($con, 'utf8');
        $before = microtime(true);

        $res = mysqli_query($con, $sql);

        $after = microtime(true);

        $timeTaken = ($after - $before) * 1000;

        if (1) {
            //TODO: FIX SQLPROFILING
//        if (SQL_PROFILING) {
            if ($res != false) {
                if (is_bool($res)) {
                    $rows = 0;
                } else {
                    $rows = mysqli_num_rows($res);
                }
                $this->logger->debug("SQL OK: [" . $sql . "] [rows:" . $rows . " " . mysqli_info($con) . "] [" . $timeTaken . "ms]", $callingInfo[0], $callingInfo[1]);
            } else {
                $rows = 0;
                $this->logger->error("SQL NOK: [" . $sql . "] [rows:" . $rows . " " . mysqli_info($con) . "] [" . $timeTaken . "ms]", $callingInfo[0], $callingInfo[1]);
                $this->logger->error("SQL:Errorno:" . mysqli_errno($con) . " Text:" . mysqli_error($con));
            }
        }
        return $res;
    }

    //OLD WAY

//    public function connectToLocalDb()
//    {
//        $con = mysqli_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB, DB_NAME_SESSIONWEB) or die("cannot connect");
//        mysqli_select_db($con, DB_NAME_SESSIONWEB) or die("cannot select DB");
//        mysqli_set_charset($con, 'utf8');
//        return $con;
//    }

//    static function sw_mysql_execute($query, $file = "", $line = "")
//    {
//        $logger = new logging();
//        $logger->debug("will execute sql...", $file, $line);
//
//        $pageTimer = new pagetimer();
//        $pageTimer->startMeasurePageLoadTime();
//        $result = $dbm->executeQuery($con,$query);
//        $logger->debug("Done execute sql...", $file, $line);
//
//        $pageTimer->stopMeasurePageLoadTime();
//
//        if (strlen($query) > 100) {
//            $queryToLog = substr($query, 0, 99) . ".... Execution time: " . $pageTimer->getTime();
//        } else {
//
//            echo $query;
//            $queryToLog = $query . ". Execution time: " . $pageTimer->getTime();
//        }
//
//        $logger->timer($queryToLog, $file, $line);
//        $logger->sql($query, $file, $line);
//        return $result;
//    }

//    static function sw_mysqli_execute($con, $query, $file = "", $line = "")
//    {
//        $logger = new logging();
//        $pageTimer = new pagetimer();
//        $pageTimer->startMeasurePageLoadTime();
//        $result = mysqli_query($con, $query);
//        $pageTimer->stopMeasurePageLoadTimeWithoutLog();
//
//        if (strlen($query) > 100) {
//            $queryToLog = "Execution time: " . $pageTimer->getTime() . ": " . substr($query, 0, 99);
//        } else {
//            $queryToLog = "Execution time: " . $pageTimer->getTime() . ": " . $query;
//        }
//        $logger->timer($queryToLog, $file, $line);
//        $logger->sql($query, $file, $line);
//        if (false === $result) {
//            $logger->sql(mysqli_error($con), $file, $line);
//            $logger->error(mysqli_error($con), $file, $line);
//        }
//        return $result;
//    }

    static function escape($con, $toEscape)
    {
        return mysqli_real_escape_string($con, $toEscape);
    }

    public function escapeAllRequestParameters($con = null)
    {
        $closeCon = null;
        if ($con == null) {
            $con = self::connectToLocalDb();
        }
        foreach ($_REQUEST as $key => $value) {
            $_REQUEST[$key] = mysqli_real_escape_string($con, $value);
        }
        return;
    }

    static function sw_mysqli_fetch_all($mysqli_result)
    {
        $resultAsArray = array();
        while ($row = mysqli_fetch_assoc($mysqli_result)) {
            $resultAsArray[] = $row;
        }
        return $resultAsArray;
    }


}

?>