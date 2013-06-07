<?php
require_once 'logging.php';
require_once 'dbHelper.php';


class StringHelper
{
    private $logger;
    private $dbHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
    }

    function str_startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function str_endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }

}

?>