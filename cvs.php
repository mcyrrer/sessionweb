<?php
session_start();
if(!session_is_registered(myusername)){
    header("location:index.php");
}

include_once('config/db.php.inc');

$start= addslashes($_REQUEST["start"]);

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

//$cur= mysql_query("SELECT * FROM completeCities ".
//    "WHERE cityName LIKE '{$start}%' LIMIT 1000");

$sqlSelect = "";
$sqlSelect .= "SELECT * ";
$sqlSelect .= "FROM   `mission` ";
$sqlSelect .= "ORDER BY updated DESC " ;
$sqlSelect .= "LIMIT  0, 30 " ;

$result = mysql_query($sqlSelect);

// Send out the data, with headers identifying it as CSV:

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=csv_1.csv");

while ($row= mysql_fetch_assoc($result)) {
    $sep= "";
    foreach ($row as $value) {
        $item= is_numeric($value) ? $value : '"'.addslashes($value).'"';
        echo $sep.$item;
        $sep= ',';
    }
    echo "\n";
}
?>