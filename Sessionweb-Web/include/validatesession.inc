<?php
if (strstr($_SERVER['PHP_SELF'], "api/") == false) {
    if (!isset($_SESSION['username'])) {
        $rootPath = checkIfRootFolder("");
        echo "$rootPath<br>";
        header("Location:".$rootPath."index.php");
        echo "Location:".$rootPath."index.php";
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

function checkIfRootFolder($pathToRoot)
{
    if (file_exists($pathToRoot.'about.php')) {
        echo "Found root at ".$pathToRoot."about.php<br>";
        return $pathToRoot;
    }
    else
    {
        echo "Not Root<br>";
        $pathToRoot .= "../";
        $pathToRoot = checkIfRootFolder($pathToRoot);
    }
    return $pathToRoot;
}

?>