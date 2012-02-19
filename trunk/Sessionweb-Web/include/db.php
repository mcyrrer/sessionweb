<?php
//include_once('../config/db.php.inc');

/*
* Create a UTF8 Mysql Connection based on sessionweb db credentials.
*/
function getMySqlConnection()
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
    mysql_set_charset('utf8');
    //mysql_query("SET NAMES utf8");
    //mysql_query("SET CHARACTER SET utf8");
    return $con;
}

function changeCharsetAndCollation($db,$charset,$collation,$db_server, $db_user, $db_password)
{
    // Script written by Vladislav "FractalizeR" Rastrusny
    // http://www.fractalizer.ru


    mysql_connect($db_server, $db_user, $db_password) or die(mysql_error());

    //Put here a list of databases you need to change charset at or leave array empty to change all existing
    $dblist = array($db);

    //If changing at all databases, which databases to skip? information_schema is mysql system databse and no need to change charset on it.
    $skip_db_list = array('information_schema', 'mysql');

    //Which charset to convert to?
    $charset = $charset;//"utf8";

    //Which collation to convert to?
    $collation = $collation;//"utf8_general_ci";

    //Only print queries without execution?
    $printonly = false;

    //Getting database names if they are not specified
    $skip_db_text = '"' . implode('", "', $skip_db_list) . '"';
    if (count($dblist) < 1) {
        $sql = "SELECT GROUP_CONCAT(`SCHEMA_NAME` SEPARATOR ',') AS FRST FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` NOT IN ($skip_db_text)";
        $result = mysql_query($sql) or die(mysql_error());
        $data = mysql_fetch_assoc($result);
        $dblist = explode(",", $data["FRST"]);
    }

    //Iterating databases
    foreach ($dblist as $dbname) {
        $sql = "SELECT CONCAT('ALTER TABLE `', t.`TABLE_SCHEMA`, '`.`', t.`TABLE_NAME`, '` CONVERT TO CHARACTER SET $charset COLLATE $collation;') as FRST FROM `information_schema`.`TABLES` t WHERE t.`TABLE_SCHEMA` = '$dbname' ORDER BY 1";
       //echo $sql.'<br>';
        $result = mysql_query($sql) or die(mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            echo "\n\n<br><br>";

            echo $row["FRST"] . "\r\n";
            if (!$printonly) {
                mysql_query($row["FRST"]) or die(mysql_error());

            }
        }
    }
}

?>

