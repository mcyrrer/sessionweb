<?php
require_once ('validatesession.inc');
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
    <link rel="stylesheet" type="text/css" media="all" href="../js/niceforms/niceforms-default.css"/>

    <script language="javascript" type="text/javascript" src="../js/jquery-1.4.4.js"></script>
    <script language="javascript" type="text/javascript" src="../js/sessionwebjquery-v25.js"></script>
</head>
<div>
    <!--    <img src='../pictures/notify-large.png' border='0' alt='Add notification' title='Notify'>-->

    <p></p>

    <p></p>

    <div>
        <fieldset>
            <legend>Notifications</legend>
            <dl>
                <dd>
                    <?php
                    if ($_GET['sessionid'] != null) {
                        if (strcmp($_GET['cmd'], 'add') == 0) {
                            if (addNotification($_GET['sessionid'], $_SESSION['username'])) {

                                echo "Notification enabled for session " . $_GET['sessionid'];
                            }
                            else
                            {
                                echo "Notification already enabled for session " . $_GET['sessionid'];
                            }
                        }
                        if (strcmp($_GET['cmd'], 'ack') == 0) {
                            $con=getMySqlConnection();

                            $versionid = getSessionVersionId($_GET['sessionid']);
                            removeNotification($versionid);
                            echo "Notification for session " . $_GET['sessionid'] . " removed.";
                            if (!$con)
                                mysql_close($con);
                            echo "<div class='notification_list'>";
                            getNotifications();
                            echo " </div>\n";
                        }
                    }
                    else
                    {
                        echo "<div class='notification_list'>";
                        getNotifications();
                        echo " </div>\n";
                    }
                    ?>
                </dd>

            </dl>
        </fieldset>


    </div>
</div>
<body>
</body>
</html>
