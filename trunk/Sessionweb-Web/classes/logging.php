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

    public function sql($logmessage, $filename = "")
    {
        $loglevel = "SQL";
        $this->writeSQLMessageToLog($loglevel, $logmessage, $filename);
    }

    public function debug($logmessage, $filename = "")
    {
        $loglevel = "DEBUG";
        $this->writeMessageToLog($loglevel, $logmessage, $filename);
    }

    public function warn($logmessage, $filename = "")
    {
        $loglevel = "INFO";
        $this->writeMessageToLog($loglevel, $logmessage, $filename);
    }

    public function warning($logmessage, $filename = "")
    {
        $loglevel = "WARN";
        $this->writeMessageToLog($loglevel, $logmessage, $filename);
    }

    public function error($logmessage, $filename = "")
    {
        $loglevel = "ERROR";
        $this->writeMessageToLog($loglevel, $logmessage, $filename);
    }

    public function fatal($logmessage, $filename = "")
    {
        $loglevel = "FATAL";
        $this->writeMessageToLog($loglevel, $logmessage, $filename);
    }

    public function writeMessageToLog($loglevel, $logmessage, $filename)
    {
        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $this->getFileName($filename) . ":" . __LINE__ . " | " . $loglevel . " | " . $logmessage . "\n";
            file_put_contents($this->logpath, $messageToWriteTofile, FILE_APPEND | LOCK_EX);
        }
    }

    public function writeSQLMessageToLog($loglevel, $logmessage, $filename)
    {
        if (in_array($loglevel, $this->loglevel)) {
            $messageToWriteTofile = $this->getDateTime() . " | " . $this->getFileName($filename) . ":" . __LINE__ . " | " . $loglevel . " | " . $logmessage . "\n";
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
