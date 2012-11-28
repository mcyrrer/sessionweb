<?php
require_once('include/loggingsetup.php');
session_start();
require_once('include/validatesession.inc');
require_once('include/commonFunctions.php.inc');
include_once('config/db.php.inc');

if (isAdmin()) {
    $file = "log/sessionweb.log";
    if (file_exists($file)) {
        $lines = file($file);

        foreach ($lines as $line_num => $line) {
            $re1 = '.*?'; # Non-greedy match on filler
            $re2 = '';//'(\\] )'; # Any Single Character 1
            $re3 = '( )'; # White Space 1
            $re4 = '(DEBUG)'; # Word 1
            $re4b = '(ERROR)';
            $re4c = '(INFO)';
            $re4d = '(WARN)';
            $re4e = '(SQL)';
            $re4f = '(FATAL)';




            if ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4 . "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: #00d5ff">';
                echo "$line";
                echo '</FONT><br>';
            }
            elseif ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4e. "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: #CCFF00">';
                echo "$line";
                echo '</FONT><br>';
            }
            elseif ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4b . "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: #ff2364">';
                echo "$line";
                echo '</FONT><br>';
            }
            elseif ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4c . "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: #00ff17">';
                echo "$line";
                echo '</FONT><br>';
            }
            elseif ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4d . "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: yellow">';
                echo "$line";
                echo '</FONT><br>';
            }
            elseif ($c = preg_match_all("/" . $re1 . $re2 . $re3 . $re4f . "/is", $line, $matches)) {
                echo '<FONT style="BACKGROUND-COLOR: red">';
                echo "$line";
                echo '</FONT><br>';
            }

            else
            {
                echo "$line<br>";
            }


        }

    }
    else
    {
        echo "no log file exist, please check that log folder is Read/write to the web server user";
    }
}
else
{
    Echo "You are not an admin user!";
    exit();
}
?>