<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}
?>