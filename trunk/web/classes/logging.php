<?php
//require_once ('dbHelper.php');
/**
 * Created by IntelliJ IDEA.
 * User: mcyrrer
 * Date: 2012-10-15
 * Time: 12:40
 * To change this template use File | Settings | File Templates.
 */
class logging
{
    private $user;
    private $logpath;
    private $logpathsql;
    private $loglevel;
    private $dbHelper;
    function __construct()
    {

        //Populate with the levels you need to have in file, "ARRAY","TIMER","SQL","DEBUG","INFO","WARN","ERROR","FATAL"
        $this->loglevel = array("SQL","ARRAY","DEBUG","INFO","WARN","ERROR","FATAL");

        $this->logpath = '';
        if (file_exists('log')) {
            $this->logpath = 'log/sessionweb.log';
            $this->logpathsql = 'log/sql.log';
        } elseif (file_exists('../log')) {
            $this->logpath = '../log/sessionweb.log';
            $this->logpathsql = '../log/sql.log';
        } elseif (file_exists('../../log')) {
            $this->logpath = '../../log/sessionweb.log';
            $this->logpathsql = '../../log/sql.log';
        } elseif (file_exists('../../../log')) {
            $this->logpath = '../../../log/sessionweb.log';
            $this->logpathsql = '../../../log/sql.log';
        } elseif (file_exists('../../../../log')) {
            $this->logpath = '../../../../log/sessionweb.log';
            $this->logpathsql = '../../../../log/sql.log';
        } elseif (file_exists('../../../../../log')) {
            $this->logpath = '../../../../../log/sessionweb.log';
            $this->logpathsql = '../../../../../log/sql.log';
        } else {
            echo "Could not find logfile log/sessionweb.log " . __FILE__;
            exit();
        }

        if (isset($_SESSION['username'])) {
            $user = $_SESSION['username'];
        } else {
            $user = "None";
        }

    }

    public function arraylog($array, $filename = "", $line = "")
    {
        $loglevel = "ARRAY";
        $array = print_r($array, true);
        $this->writeMessageToLog($loglevel, $array, $filename, $line);
    }

    public function sql($logmessage, $filename = "", $line = "")
    {
        $loglevel = "SQL";
        $this->writeSQLMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    public function timer($logmessage, $filename = "", $line = "")
    {
        $loglevel = "TIMER";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    public function debug($logmessage, $filename = "", $line = "", $trace = false)
    {
        $loglevel = "DEBUG";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
        if ($trace) {
            try {
                throw new Exception();
            } catch (Exception $e) {

                $this->writeMessageToLog($loglevel, $e, null, null);
            }
        }
    }

    public function info($logmessage, $filename = "", $line = "")
    {
        $loglevel = "INFO";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    public function warn($logmessage, $filename = "", $line = "")
    {
        $loglevel = "WARN";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    public function warning($logmessage, $filename = "", $line = "")
    {
        $this->warn($logmessage, $filename, $line);
    }

    public function error($logmessage, $filename = "", $line = "")
    {
        $loglevel = "ERROR";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
        try {
            throw new Exception();
        } catch (Exception $e) {

            $this->writeMessageToLog($loglevel, $e, null, null);
        }
    }

    public function fatal($logmessage, $filename = "", $line = "")
    {
        $loglevel = "FATAL";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
        try {
            throw new Exception();
        } catch (Exception $e) {

            $this->writeMessageToLog($loglevel, $e, null, null);
        }
    }

    private function writeMessageToLog($loglevel, $logmessage, $filename, $line)
    {
        $this->writeMessageToFile($loglevel, $logmessage, $filename, $line);
        //$this->writeMessageToDb($loglevel, $logmessage, $filename, $line);
    }

    private function writeMessageToFile($loglevel, $logmessage, $filename, $line)
    {
        if (isset($_SESSION['username']))
            $username = $_SESSION['username'];
        else
            $username = "";

        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $loglevel . " | " . $this->getFileName($filename) . ":" . $line . " | " . $username . " | " . $logmessage . "\n";
            file_put_contents($this->logpath, $messageToWriteTofile, FILE_APPEND | LOCK_EX);
        }
    }

//    private function writeMessageToDb($loglevel, $logmessage, $filename, $line)
//    {
//        $this->dbHelper = new dbHelper();
//        $dbCon = $this->dbHelper->db_getMySqliConnection();
//        if (isset($_SESSION['username']))
//            $username = $_SESSION['username'];
//        else
//            $username = "";
//
//        $loglevel=$this->dbHelper->escape($dbCon,$loglevel);
//        $logmessage=$this->dbHelper->escape($dbCon,$logmessage);
//        $logLine =  $this->getFileName($filename) . ":" . $line;
//        if (in_array($loglevel, $this->loglevel)) {
//            $query="INSERT INTO sessionweb_log (level, line, logrow, user) VALUES ('".$loglevel."', '".$logLine."', '".$logmessage."', '".$username."')";
//            $this->dbHelper->sw_mysqli_execute($dbCon,$query);
//        }
//       mysqli_close($dbCon);
//    }

    private function writeSQLMessageToLog($loglevel, $logmessage, $filename, $line)
    {
        if (isset($_SESSION['username']))
            $username = $_SESSION['username'];
        else
            $username = "";

        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $loglevel . " | " . $this->getFileName($filename) . ":" . $line . " | " . $username . " | " . $logmessage . "\n";
            file_put_contents($this->logpathsql, $messageToWriteTofile, FILE_APPEND | LOCK_EX);
        }
    }

    private function getFileName($filename)
    {
        if (strlen($filename) > 45) {
            return substr($filename, -43);
        } else
            return $filename;
    }

    private function getDateTime()
    {
        return date('Ymd H:i:s', time());
    }


    function MakePrettyException(Exception $e)
    {
        $trace = $e->getTrace();

        $result = 'Exception: "';
        $result .= $e->getMessage();
        $result .= '" @ ';
        if ($trace[0]['class'] != '') {
            $result .= $trace[0]['class'];
            $result .= '->';
        }
        $result .= $trace[0]['function'];
        $result .= '();<br />';

        return $result;
    }
}

