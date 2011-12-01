<?php
require_once ('loggedincheck.php');
require_once('loggingsetup.php');
require_once ('../config/db.php.inc');
include_once('commonFunctions.php.inc');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Notifications</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="../css/sessionwebcss.css">
    <script language="javascript" type="text/javascript" src="../js/jquery-1.4.4.js"></script>
    <script language="javascript" type="text/javascript" src="../js/sessionwebjquery.js"></script>
</head>
<div id='notification' class="notification">
    <h1 class="notification">Notifications</h1>
<!--    <img src='../pictures/notify-large.png' border='0' alt='Add notification' title='Notify'>-->

    <p></p>

    <p></p>

    <div>
        <?php
        if ($_GET['sessionid'] != null) {
            if (addNotification($_GET['sessionid'], $_SESSION['username'])) {

                echo "Notification enabled for session " . $_GET['sessionid'];
            }
            else
            {
                echo "Notification already enabled for session " . $_GET['sessionid'];
            }
        }
        else
        {
            echo "<div class='notification_list'>";
            echo "<h2>Notification list</h2>";
            getNofifications();
            echo " </div>\n";
        }
        ?>
    </div>
</div>
<body>
</body>
</html>
