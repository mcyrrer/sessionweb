<?php
session_start();


include("include/header.php.inc");
include_once('config/db.php.inc');

echo "<h2>Sessionweb is using the following open source software:</h2><br>";

echo "CKEditor: http://ckeditor.com<br>";

echo "jQuery-File-Upload: https://github.com/blueimp/jQuery-File-Upload/<br>";
    
echo "ColorBox: http://jacklmoore.com/colorbox/<br>";

include("include/footer.php.inc");
?>
