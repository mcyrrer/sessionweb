<?php
require_once 'logging.php';
require_once 'dbHelper.php';


class GraphHelper
{
    private $logger;
    private $dbHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
    }

    public function googlePieChart($valuesAndLables, $title = null, $sizeX = 450, $sizeY = 150, $colors = array('FF0000', '00FF00', '0000FF', '000000'))
    {
        $this->logger->debug("Title: ".$title, __FILE__, __LINE__);

        if (is_array($valuesAndLables)) {
            $barTitles = "";
            $barValues = "";
            foreach ($valuesAndLables as $key => $value) {
                $barTitles = $barTitles . "|" . $key;
                $barValues = $barValues . "," . $value;
            }
            //echo $barValues;
            $barTitles = substr($barTitles, 1, strlen($barTitles));
            $barValues = substr($barValues, 1, strlen($barValues));

            $colors = implode("|", $colors);

            if ($title != null) {
                $title = "chtt=$title";
            } else {
                $title = "";
            }

            $imgURL = "http://chart.apis.google.com/chart?cht=p3&amp;chd=s:Uf9a&amp;chs=" . $sizeX . "x" . $sizeY . "&amp;chd=t:$barValues&amp;chl=$barTitles&amp;chco=$colors&$title";
            $this->logger->debug("Created google pie chart: " . $imgURL, __FILE__, __LINE__);
            return $imgURL;
        } else {
            $this->logger->error("Create Google pie chart: Parameter with values and lables is not an array!", __FILE__, __LINE__);
            $this->logger->debug(print_r($valuesAndLables,true), __FILE__, __LINE__);
            return "ERROR";
        }
    }

}

?>