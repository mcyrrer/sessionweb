<?php

class MySqlExecuter
{
    /**
     * Execute a file of sql statements. Have to have a active mysql link.
     * @param  $sqlfile File with sql statements to execute
     * @param string $sqldelimiter
     * @return array|null array with errors or null if file does not exist
     */
    function multiQueryFromFile($sqlfileName, $sqldelimiter = ';')
    {
        $error_log_from_sql_execution = array();
        set_time_limit(0);

        if (is_file($sqlfileName) === true) {
            $sqlfile = fopen($sqlfileName, 'r');

            if (is_resource($sqlfile) === true) {


                $query = array();
                //echo "<table cellspacing='3' cellpadding='3' border='0'>";

                while (feof($sqlfile) === false) {
                    $query[] = fgets($sqlfile);

                    if (preg_match('~' . preg_quote($sqldelimiter, '~') . '\s*$~iS', end($query)) === 1) {
                        $query = trim(implode('', $query));

                        if (mysql_query($query) === false) {
                            $error = "SQL: " .$query . "<br> Error Msg: " . mysql_error();
                            array_push($error_log_from_sql_execution, $error);
                             //echo '<tr><td>ERROR:</td><td> ' . $query . '</td></tr>';
                        } else {
                            // echo '<tr><td>SUCCESS:</td><td>' . $query . '</td></tr>';
                        }

                        while (ob_get_level() > 0) {
                            ob_end_flush();
                        }

                        flush();
                    }

                    if (is_string($query) === true) {
                        $query = array();
                    }
                }
                // echo "</table>";

                fclose($sqlfile);
                return $error_log_from_sql_execution;
            }
        }
        return null;
    }
}
