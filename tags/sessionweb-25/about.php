<?php
session_start();


include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');
include("include/header.php.inc");

echo "<h2>Sessionweb is using the following open source software:</h2><br>";

echo "CKEditor: http://ckeditor.com<br>";
echo "jQuery: http://jQuery.com<br>";
echo "jQuery-File-Upload: https://github.com/blueimp/jQuery-File-Upload/<br>";
echo "jQuery-Autosave: http://archive.plugins.jquery.com/content/jquery-autosave-110<br>";
echo "jquery.getparams: http://???<br>";
echo "jquery.valdate: http://bassistance.de/jquery-plugins/jquery-plugin-validation/<br>";
echo "niceforms: http://www.emblematiq.com/projects/niceforms/<br>";
echo "ColorBox: http://jacklmoore.com/colorbox/<br>";
echo "Icons: http://openiconlibrary.sourceforge.net/<br><br>";
echo "<div>Design inspired from nodethirtythree.com</div>";
include("include/footer.php.inc");
?>
