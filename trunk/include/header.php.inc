<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\">\n";
echo "      <title>Sessionweb</title>\n";
echo "  </head>\n";
echo "  <body>\n";
echo "      <a href=\"index.php\">Main page</a> |";
echo "      <a href=\"settings.php\">Settings</a> |";



if(isset($_SESSION['user']))
{
	echo "      <a href=\"index.php?logout=yes\">Log out</a> |";
}


echo "<p><img src=\"pictures/logo.png\" alt=\"Sessionweb logo\"></p>\n";
?>