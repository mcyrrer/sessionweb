<?php
session_start();
if (!session_is_registered(myusername)) {
    header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');

?>
<table>
    <tr>
        <td valign="top">
            <h2>Graph type</h2>

            <div>
                <img src="pictures/line-graph-medium.png" alt=""><br>
                <a href="#" id="sessionsperday">Sessions per day</a>
            </div>
            <div>
                <img src="pictures/bar-graph-medium.png" alt=""><br>
                <a href="#" id="sessionspersprint">Sessions per sprint</a>
            </div>
            <div>

                <img src="pictures/pie-graph-medium.png" alt=""><br>
                <a href="#" id="timedistribution">Time distribution</a>
            </div>
        </td>
        <td valign="top">
            <form name="input" action="html_form_action.asp" method="get">
               <div id="statistic_options">
                    statistic_options
                </div>
                <input type="submit" value="Submit"/>
            </form>
            <div id="statistic_result">
                statistic_result
            </div>
        </td>
    </tr>
</table>

