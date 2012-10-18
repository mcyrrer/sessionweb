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
        //Populate with the levels you need to have in file, "SQL","DEBUG","INFO","WARN","ERROR","FATAL"
        $this->loglevel = array("SQL", "DEBUG", "INFO", "WARN", "ERROR", "FATAL");

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

    public function sql($logmessage, $filename = "", $line="")
    {
        $loglevel = "SQL";
        $this->writeSQLMessageToLog($loglevel, $logmessage, $filename, $line);
    }

    public function debug($logmessage, $filename = "",$line="")
    {
        $loglevel = "DEBUG";
        $this->writeMessageToLog($loglevel, $logmessage, $filename,$line);
    }

    public function warn($logmessage, $filename = "",$line="")
    {
        $loglevel = "INFO";
        $this->writeMessageToLog($loglevel, $logmessage, $filename,$line);
    }

    public function warning($logmessage, $filename = "",$line="")
    {
        $loglevel = "WARN";
        $this->writeMessageToLog($loglevel, $logmessage, $filename,$line);
    }

    public function error($logmessage, $filename = "",$line="")
    {
        $loglevel = "ERROR";
        $this->writeMessageToLog($loglevel, $logmessage, $filename,$line);
    }

    public function fatal($logmessage, $filename = "",$line="")
    {
        $loglevel = "FATAL";
        $this->writeMessageToLog($loglevel, $logmessage, $filename,$line);
    }

    public function writeMessageToLog($loglevel, $logmessage, $filename,$line)
    {
        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $this->getFileName($filename) . ":" . $line . " | " . $loglevel . " | " . $logmessage . "\n";
            file_put_contents($this->logpath, $messageToWriteTofile, FILE_APPEND | LOCK_EX);
        }
    }

    public function writeSQLMessageToLog($loglevel, $logmessage, $filename,$line)
    {
        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $this->getFileName($filename) . ":" . $line . " | " . $loglevel . " | " . $logmessage . "\n";
            file_put_contents($this->logpathsql, $messageToWriteTofile, FILE_APPEND | LOCK_EX);
        }
    }

    private function getFileName($filename)
    {
        if (strlen($filename) > 30) {
            return substr($filename, -25);
        } else
            return $filename;
    }

    private function getDateTime()
    {
        return date('Ymd H:m:s', time());
    }
}
