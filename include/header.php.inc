<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\">\n";
echo "      <title>Sessionweb</title>\n";

echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/sessionwebcss.css\" />\n";

echo "     <script src=\"js/jquery-1.4.4.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/sessionwebjquery.js\" type=\"text/javascript\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjs.js\"></script>\n";

if(strstr($_SERVER['PHP_SELF'],"session.php")!=false)
{

	if(strcmp($_REQUEST["command"],"new")==0 || strcmp($_REQUEST["command"],"edit")==0)
	{
		echo "<!-- \n";
		echo "                  Include the WYSIWYG javascript files\n";
		echo "-->\n";
		echo "              <script type=\"text/javascript\" src=\"editor/scripts/wysiwyg.js\"></script>\n";
		echo "              <script type=\"text/javascript\" src=\"editor/scripts/wysiwyg-settings.js\"></script>\n";
		echo "              <script type=\"text/javascript\">\n";
		echo "                  WYSIWYG.attach('textarea1',small); // default setup\n";
		echo "                  WYSIWYG.attach('textarea2',small); // default setup\n";
		echo "              </script>\n";
	}
}
echo "  </head>\n";
echo "  <body>\n";
if($_SESSION['user']!="")
{
	echo "      [".$_SESSION['user']."]";
}
echo "      <a href=\"index.php\" id=\"url_index\">Main page</a> |";
echo "      <a href=\"session.php?command=new\" id=\"url_newsession\">New session</a> |";
echo "      <a href=\"list.php\" id=\"url_list\">List sessions</a> |";
echo "      <a href=\"settings.php\" id=\"url_settings\">Settings</a> |";



if(isset($_SESSION['user']))
{
	echo "      <a href=\"index.php?logout=yes\" id=\"url_logout\">Log out</a> |";
}


echo "<p><img src=\"pictures/logo.png\" alt=\"Sessionweb logo\"/></p>\n";
?>