<?php

session_start();
require_once('../../../include/validatesession.inc');


echo "<center>";
echo "<img src='../../../pictures/dialog-question-large.png' alt=''>";

echo "<h2>Free text search help</h2>";
echo "+ stands for AND<br>
- stands for NOT<br>
[no operator] implies OR";
echo "</center>";
?>