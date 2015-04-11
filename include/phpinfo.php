<?php
require_once('../classes/autoloader.php');

require_once('loggingsetup.php');
if ($_SESSION['useradmin'] == 1) {
    phpinfo();
} else {
    echo "you are not authorized to view this page";
}
?>