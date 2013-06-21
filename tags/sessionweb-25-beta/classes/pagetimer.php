<?php
require_once 'logging.php';

/**
 * Class to track load time of a page
 */
class pagetimer
{
    private $logger;
    private $start;
    private $loadTime;

    function __construct($sessionid = null)
    {
        $this->logger = new logging();
    }

    /**
     * Start to measure load time. Should be called at the top of a page
     */
    public function startMeasurePageLoadTime()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $this->start = $time;
    }

    /**
     * Stop to measure load time. Should be called at the bottom of the page just before the toString function.
     * Also add it to the log if debug level is at debug of if load time is > 5 sec log it as warn
     */
    public function stopMeasurePageLoadTime($comment="",$file="",$line="")
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;

        $this->loadTime = round(($finish - $this->start), 4);

        if ($this->loadTime > 5) {
            $this->logger->warning("$comment Load time for " . $this->getUrl() . " " . $this->loadTime . " seconds", $file, $line);
        } else {
            $this->logger->timer("$comment Load time for " . $this->getUrl() . " " . $this->loadTime . " seconds", $file, $line);
        }
    }

    /**
     * Stop to measure load time. Should be called at the bottom of the page just before the toString function.
     * Also add it to the log if debug level is at debug of if load time is > 5 sec log it as warn
     */
    public function stopMeasurePageLoadTimeWithoutLog($comment="",$file="",$line="")
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;

        $this->loadTime = round(($finish - $this->start), 4);
    }

    /**
     * Echo loadtime to page
     */
    public function echoTime()
    {
        echo '<p>Page generated in ' . $this->loadTime . ' seconds.</p';
    }

    public function getTime()
    {
        return $this->loadTime;
    }

    private function getUrl()
    {
        $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}

?>