<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\">\n";
echo "      <title>Sessionweb</title>\n";
if(strstr($_SERVER['PHP_SELF'],"session.php")!=false)
{
		echo "     <script type=\"text/javascript\" src=\"js/sessionwebjs.js\"></script>\n";
//	echo "<script type=\"text/javascript\">";
//	echo "function validate_required(field,alerttxt)";
//	echo "{";
//	echo "with (field)";
//	echo "  {";
//	echo "  if (value==null||value==\"\")";
//	echo "    {";
//	echo "    alert(alerttxt);return false;";
//	echo "    }";
//	echo "  else";
//	echo "   {";
//	echo "    return true;";
//	echo "    }";
//	echo " }";
//	echo "}";
//
//	echo "function validate_form(thisform)";
//	echo "{";
//	echo "with (thisform)";
//	echo "  {";
//	echo " if (validate_required(title,\"Title must be filled out!\")==false)";
//	echo "  {title.focus();return false;}";
//	echo "  }";
//	echo "}";
//	echo "</script>";

	$new = $_GET["new"];
	if($new==true)
	{
		echo "<!-- \n";
		echo "                  Include the WYSIWYG javascript files\n";
		echo "-->\n";
		echo "              <script type=\"text/javascript\" src=\"editor/scripts/wysiwyg.js\"></script>\n";
		echo "              <script type=\"text/javascript\" src=\"editor/scripts/wysiwyg-settings.js\"></script>\n";
		echo "              <script type=\"text/javascript\">\n";
		echo "                  WYSIWYG.attach('textarea1',full); // default setup\n";
		echo "                  WYSIWYG.attach('textarea2',full); // default setup\n";
		echo "              </script>\n";
	}
}
echo "  </head>\n";
echo "  <body>\n";
echo "      <a href=\"index.php\">Main page</a> |";
echo "      <a href=\"session.php?new=true\">New session</a> |";
echo "      <a href=\"list.php\" onsubmit=\"return validate_form(this)\">List sessions</a> |";
echo "      <a href=\"settings.php\">Settings</a> |";



if(isset($_SESSION['user']))
{
	echo "      <a href=\"index.php?logout=yes\">Log out</a> |";
}


echo "<p><img src=\"pictures/logo.png\" alt=\"Sessionweb logo\"></p>\n";
?>