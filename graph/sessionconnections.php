<?php
session_start();
if (!session_is_registered(myusername)) {
    header("location:../index.php");
}
include_once('../config/db.php.inc');
include_once ('../include/commonFunctions.php.inc');
$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$sessionid = $_GET['sessionid'];
$versionid = GetVersionidFromSessionid($sessionid);

$sessionConnectiosTo = array();
$sessionConnectiosFrom = array();

$nbrOfConnections = 1;

getSessionsLinkedToMasterSession($versionid);
getSessionsLinkedFromMasterSession($versionid);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="latin-1"/>
    <title>Session connection graph</title>
    <link rel="stylesheet" href="../css/wordcloud.css">
    <script type="text/javascript" src="../js/jquery-1.4.4.js"></script>
    <script type="text/javascript" src="../js/sessionwebjquery.js"></script>

    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
        google.load('visualization', '1', {packages:['orgchart']});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            // Create and populate the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');
<?php
            echo "data.addRows($nbrOfConnections);\n";
        $idLevel = 0;
        //TODO: 1. Generate an array with key NodeId and value sessionid.
        //TODO: 2. Printa ut arrayn skapad ovan.
        echoDataCells($sessionConnectiosTo);
        ?>
//            data.setCell(0, 0, 'Mike');
//            data.setCell(0, 2, 'The President');
//            data.setCell(1, 0, 'Jim', 'Jim');
//            data.setCell(1, 1, 'Mike');
//            data.setCell(2, 0, 'Alice');
//            data.setCell(2, 1, 'Mike');
//            data.setCell(3, 0, 'Bob');
//            data.setCell(3, 1, 'Jim');
//            data.setCell(3, 2, 'Bob Sponge');
//            data.setCell(4, 0, 'Carol');
//            data.setCell(4, 1, 'Bob');
//            data.setCell(5, 0, 'Mattias');
//            data.setCell(5, 1, 'Bob');

            // Create and draw the visualization.
            new google.visualization.OrgChart(document.getElementById('chart_div')).
                    draw(data, {allowHtml: true});
        }
    </script>

</head>

<body>
<div id='chart_div'></div>
<?php

//print_r($sessionConnectiosTo);
//print_r($sessionConnectiosFrom);
//echo $nbrOfConnections;
mysql_close($con);
?>
</body>

</html>

<?php
function echoDataCells($sessionConnectios)
{
    if (!is_array($sessionConnectios)) {
        GLOBAL $idLevel;
        echo $sessionConnectios;
        echo "data . setCell($idLevel, 0, 'SessionsId');\n";
        echo "data . setCell($idLevel, 1, 'SessionLänkadTill');\n";
        $idLevel++;

    }
    else
    {
        foreach ($sessionConnectios as $sessions)
        {
            echoDataCells($sessions);
        }
    }
}


function getSessionsLinkedToMasterSession($versionid)
{
    //echo "<br>processing versionid: $versionid<br>";
    $sessions = array();
    $sql = "SELECT * FROM `sessionwebos`.`mission_sessionsconnections` where linked_from_versionid=$versionid;";
    $result = mysql_query($sql);

    if (!$result) {
        //        echo "messy";
        return null;
    }
    else
    {
        while ($row = mysql_fetch_array($result)) {
            $versionIdToGetConnectionsFor = $row['linked_to_versionid'];


            //            echo "$versionid->$versionIdToGetConnectionsFor\n";
            //
            GLOBAL $sessionConnectiosTo;
            GLOBAL $nbrOfConnections;
            if (array_key_exists($versionid, $sessionConnectiosTo) == false) {
                $sessionConnectiosTo[$versionid] = array($versionIdToGetConnectionsFor);
                $nbrOfConnections++;
            }
            else
            {
                //                echo "key already exist...$versionid->$versionIdToGetConnectionsFor (1)\n";
                array_push($sessionConnectiosTo[$versionid], $versionIdToGetConnectionsFor);
                $nbrOfConnections++;
            }

            if (array_key_exists($versionIdToGetConnectionsFor, $sessionConnectiosTo) == false) {
                getSessionsLinkedToMasterSession($versionIdToGetConnectionsFor);

            }
            else
            {
                //                echo "key already exist (1)";
            }


        }
    }
    return $sessions;
}

function getSessionsLinkedFromMasterSession($versionid)
{

    $sessions = array();
    $sql = "SELECT * FROM `sessionwebos`.`mission_sessionsconnections` where linked_to_versionid=$versionid;";
    $result = mysql_query($sql);

    if (!$result) {
        echo "messy";
        return null;
    }
    else
    {
        while ($row = mysql_fetch_array($result)) {
            $versionIdToGetConnectionsFor = $row['linked_from_versionid'];


            //echo "$versionid->$versionIdToGetConnectionsFor\n";

            GLOBAL $sessionConnectiosFrom;
            GLOBAL $nbrOfConnections;
            if (array_key_exists($versionid, $sessionConnectiosFrom) == false) {
                $sessionConnectiosFrom[$versionid] = array($versionIdToGetConnectionsFor);
                $nbrOfConnections++;
            }
            else
            {
                //                echo "key already exist...$versionid->$versionIdToGetConnectionsFor (2)\n";
                array_push($sessionConnectiosFrom[$versionid], $versionIdToGetConnectionsFor);
                $nbrOfConnections++;
            }

            if (array_key_exists($versionIdToGetConnectionsFor, $sessionConnectiosFrom) == false) {
                getSessionsLinkedFromMasterSession($versionIdToGetConnectionsFor);
            }
            else
            {
                //                echo "key already exist (2)";
            }


        }
    }
    return $sessions;
}


?>
