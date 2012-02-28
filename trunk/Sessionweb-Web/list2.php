<?php
session_start();
require_once('include/validatesession.inc');
require_once('config/db.php.inc');
require_once('include/header.php.inc');
require_once('include/db.php');
?>

    <div id="content">
        <!-- Primary content: Stuff that goes in the primary content column (by default, the left column) -->
        <div id="primarycontainer">
            <div id="primarycontent">
                <!-- Primary content area start -->
                <table id="flexgrid1"></table>


                <!-- Primary content area end -->
            </div>
        </div>
    </div>
<?php
require_once('include/footer.php.inc');
?>