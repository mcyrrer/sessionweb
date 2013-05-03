<?php
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

    function __construct()
    {
        //Populate with the levels you need to have in file, "ARRAY","TIMER","SQL","DEBUG","INFO","WARN","ERROR","FATAL"
        $this->loglevel = array("ARRAY","SQL", "DEBUG", "INFO", "WARN", "ERROR", "FATAL");

        $this->logpath = '';
        if (file_exists('log')) {
            $this->logpath = 'log/sessionweb.log';
            $this->logpathsql = 'log/sql.log';
        } elseif (file_exists('../log')) {
            $this->logpath = '../log/sessionweb.log';
            $this->logpathsql = '../log/sql.log';
        }
        elseif (file_exists('../../log')) {
            $this->logpath = '../../log/sessionweb.log';
            $this->logpathsql = '../../log/sql.log';
        }
        elseif (file_exists('../../../log')) {
            $this->logpath = '../../../log/sessionweb.log';
            $this->logpathsql = '../../../log/sql.log';
        }
        elseif (file_exists('../../../../log')) {
            $this->logpath = '../../../../log/sessionweb.log';
            $this->logpathsql = '../../../../log/sql.log';
        }
        elseif (file_exists('../../../../../log')) {
            $this->logpath = '../../../../../log/sessionweb.log';
            $this->logpathsql = '../../../../../log/sql.log';
        }
        else {
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

        $this->writeMessageToLog($loglevel, implode('->',$array), $filename, $line);
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

    public function debug($logmessage, $filename = "", $line = "")
    {
        $loglevel = "DEBUG";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
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
       // $this->writeMessageToLog($loglevel, debug_string_backtrace(), $filename, $line);
       // $this->writeMessageToLog($loglevel, "!!!!!", $filename, $line);
    }

    public function fatal($logmessage, $filename = "", $line = "")
    {
        $loglevel = "FATAL";
        $this->writeMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    private function writeMessageToLog($loglevel, $logmessage, $filename, $line)
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

    function debug_string_backtrace() {
        //ob_start();
        var_dump(debug_backtrace());
        $trace = ob_get_contents();
        //ob_end_clean();

        // Remove first item from backtrace as it's this function which
        // is redundant.
        //$trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

        // Renumber backtrace items.
        //$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

        return $trace;
    }
}