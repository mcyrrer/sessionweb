<?php

class MySqliExecuter
{
    /**
     * Execute a file of sql statements. Have to have a active mysql link.
     * @param  $sqlfile File with sql statements to execute
     * @param string $sqldelimiter
     * @return array|null array with errors or null if file does not exist
     */
    function multiQueryFromFile($con,$sqlfileName, $dbname, $createDb=false, $sqldelimiter = ';')
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
                        $query = str_replace("sessionwebos", $dbname, $query);
                        //echo $query;
                        $skipQuery = false;

                        if ($createDb == false) {
                            if (strstr($query, "DROP SCHEMA") != false || strstr($query, "CREATE SCHEMA ") != false) {
                                $skipQuery = true;
                            }
                        }

                        if ($skipQuery == false) {
                            if (mysqli_query($con,$query) === false) {
                                $error = "SQL: " . $query . "<br> Error Msg: " . mysqli_error($con);
                                array_push($error_log_from_sql_execution, $error);
                                //echo '<tr><td>ERROR:</td><td> ' . $query . '</td></tr>';
                            } else {
                                // echo '<tr><td>SUCCESS:</td><td>' . $query . '</td></tr>';
                            }
                        }
                        else
                        {
                            if ($createDb == false) {
                                echo "Create database query skiped.<br>";
                            }
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
