<?php
//include_once('../config/db.php.inc');

/*
* Create a UTF8 Mysql Connection based on sessionweb db credentials.
*/
function getMySqlConnection()
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    mysql_query("SET NAMES utf8");
    mysql_query("SET CHARACTER SET utf8");
    return $con;
}

