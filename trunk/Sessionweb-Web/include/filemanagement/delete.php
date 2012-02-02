    <?php
require_once('../../include/loggingsetup.php');
include_once("../../include/loggedincheck.php");

include "../../config/db.php.inc";
$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
$sql = "DELETE FROM mission_attachments WHERE id = " . $_GET['id'];
$result = mysql_query($sql) or die( 'Error, query failed');

mysql_close();

header('Content-type: application/json');
echo json_encode(true);

exit;

?>


