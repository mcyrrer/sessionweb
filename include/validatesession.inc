<?php
if (strstr($_SERVER['PHP_SELF'],"api/")==false) {
    if (!isset($_SESSION['username'])) {
        header("Location:index.php");
        echo "You are not logged in!";
        die();
    }
}
else
{
    if (!isset($_SESSION['username'])) {
        header("HTTP/1.0 401 Unauthorized");
        $response['code'] = UNAUTHORIZED;
        $response['text'] = "UNAUTHORIZED";
        echo json_encode($response);
        die();
    }
}
?>