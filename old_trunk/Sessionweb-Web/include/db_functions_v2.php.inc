<?php

/**
 * Create a sessionid and return the value created
 * @return mixed sessionid on success else null
 */
function swCreateNewSessionId()
{
    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO sessionid ";
    $sqlInsert .= "            (`createdby`) ";
    $sqlInsert .= "VALUES      ('" . $_SESSION['username'] . "') ";

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "DB Error: " . mysql_error() . "<br/>";
        debug_backtrace();
        return null;
    }

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   sessionid ";
    $sqlSelect .= "WHERE  createdby = '" . $_SESSION['username'] . "' ";
    $sqlSelect .= "ORDER  BY sessionid DESC ";
    $sqlSelect .= "LIMIT  1";

    $result = mysql_query($sqlSelect);

    if ($result) {
        $row = mysql_fetch_array($result);
        $sessionid = $row["sessionid"];
    } else {
        echo "DB Error: " . mysql_error() . "<br/>";
        debug_backtrace();
        return null;
    }

    return $sessionid;
}