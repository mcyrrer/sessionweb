<?php
/**
 * API to create a set a google pie chart for metrics
 *
 */

define("X", 450);
define("Y", 150);
define("TITLE", "");


session_start();

require_once('../../../include/validatesession.inc');
require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/GraphHelper.php');

$logger = new logging();
$gh = new GraphHelper();

if (isset($_REQUEST['data'])) {
//    public function googlePieChart($valuesAndLables, $title = null, $sizeX = 450, $sizeY = 150, $colors = array('FF0000', '00FF00', '0000FF'))
    $data = $_REQUEST['data'];
//    $data = json_decode($data,true);
    $title = TITLE;
    if (isset($_REQUEST['title'])) {
        $title = $_REQUEST['title'];
    }

    $sizeX = X;
    if (isset($_REQUEST['x'])) {
        $sizeX = $_REQUEST['x'];
    }

    $sizeY = Y;
    if (isset($_REQUEST['y'])) {
        $sizeY = $_REQUEST['y'];
    }

    foreach($data as $key=>$value)
    {
        $label = $key." ".$value."%";
        $data2[$label]=$value;
    }
    if (isset($_REQUEST['colors']) && is_array($_REQUEST['colors'])) {
        $response = $gh->googlePieChart($data2, $title, $sizeX, $sizeY);
    } else {
        $response = $gh->googlePieChart($data2, $title, $sizeX, $sizeY);
    }


} else {
    $logger->debug("Could not create google pie chart, parameter data is not correct", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo $response;
