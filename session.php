<?php
if (isset($_REQUEST['sessionid'])) {
    if (isset($_REQUEST['command'])) {
        if (strcmp($_REQUEST['command'], "edit")) {
            header('Location: edit.php?sessionid=' . $_REQUEST['sessionid']);

        } elseif (strcmp($_REQUEST['command'], "view")) {
            header('Location: view.php?sessionid=' . $_REQUEST['sessionid']);
        } elseif (strcmp($_REQUEST['command'], "debrief")) {
            header('Location: debrief.php?sessionid=' . $_REQUEST['sessionid']);

        } else {
            echo "sorry, new page for view and edit is view.php and edit.php";
            die();
        }
    }
    else
        echo "sorry, new page for view and edit is view.php and edit.php";
}
else
    echo "sorry, new page for view and edit is view.php and edit.php";
