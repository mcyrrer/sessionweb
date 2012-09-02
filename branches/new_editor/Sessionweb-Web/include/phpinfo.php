<?php

require_once('loggingsetup.php');
session_start();
require_once('validatesession.inc');
  if ($_SESSION['useradmin'] == 1) {
       phpinfo();
  }
else
{
    echo "you are not authorized to view this page";
}
?>