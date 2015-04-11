<?php
$pathInfo = pathinfo(__FILE__);
$dir = $pathInfo['dirname'] . '/';

if (!file_exists($dir . '/../config/db.php.inc')) {
    header("Location: install/install.php");
    exit();
}

spl_autoload_register('my_autoloader');
register_shutdown_function('shutdown');

/*if (isset($_COOKIE['PHPSESSID']) && count($_COOKIE['PHPSESSID'])>0) {
    session_id($_COOKIE['PHPSESSID']);
} else {
    $id=uniqid('sw');
    session_id($id);
}*/
session_start();


if (isPageANonSessionPage()
) {
    return;
}

if (!isset($_SESSION['validated'])) {
    $validator = new ValidateSession();
    $validator->validate();
    $_SESSION['validated'] = true;
}


function my_autoloader($class_name)
{

    $pathInfo = pathinfo(__FILE__);
    $dir = $pathInfo['dirname'] . '/';

    require_once($dir . '/../config/db.php.inc');
    include_once($dir . '/../config/auth.php.inc');


    if (file_exists($dir . $class_name . '.php')) {
        require_once($dir . $class_name . '.php');
    }
}

function shutdown()
{
    $errorLevel = error_reporting();
    error_reporting(E_ERROR);
    if (isset($_SESSION['mysqliCon'])) {
        if (is_a($_SESSION['mysqliCon'], 'mysqli')) {
            try {
//                my_autoloader('logging');
//                $logger = new logging();
                mysqli_close($_SESSION['mysqliCon']);
                unset($_SESSION['mysqliCon']);
//                $logger->info("Disconnected from db");

            } catch (Exception $e) {
                unset($_SESSION['mysqliCon']);
            }
        } else {
            unset($_SESSION['mysqliCon']);
        }
    }
    error_reporting($errorLevel);
    unset($_SESSION['validated']);
}

/**
 * Is the page a page that does not require the user to be logged in?
 * @return bool
 */
function isPageANonSessionPage()
{
    return strpos($_SERVER['SCRIPT_FILENAME'], "install/install.php") !== false ||
    strpos($_SERVER['SCRIPT_FILENAME'], "install/upgrade.php") !== false ||
    strpos($_SERVER['SCRIPT_FILENAME'], "/checklogin.php") !== false;
}
