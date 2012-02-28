<?php
require_once ('include/loggedincheck.php');
require_once ('config/db.php.inc');
session_start();
require_once('include/validatesession.inc');

$table = 'cvs'; // table you want to export
$file = 'sessionweb.cvs'; // csv name.

$link = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("Can not connect." . mysql_error());
mysql_select_db(DB_NAME_SESSIONWEB) or die("Can not connect.");

$result = mysql_query("SHOW COLUMNS FROM ".$table."");
$i = 0;

if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $csv_output .= $row['Field'].";";
        $i++;}
}
$csv_output .= "\n";
$values = mysql_query("SELECT * FROM ".$table."");

while ($rowr = mysql_fetch_row($values)) {
    for ($j=0;$j<$i;$j++) {
        $line = str_replace('\n','',$rowr[$j]);
        $csv_output .= $line."; ";
    }
    $csv_output .= "\n";
}

$filename = $file."_".date("d-m-Y_H-i",time());

header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");

print $csv_output;

exit;
?>