<?php
if(!isset($_SESSION['username'])) {
    header("Location:index.php");
    echo "You are not logged in!";
    die();
}
?>