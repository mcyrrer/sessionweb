<?php
//if(is_file("install.php"))
//{
//    header("Location: install.php");
//    exit();
//}

$logout = $_GET["logout"];
session_start();

if($logout=="yes")
{
	session_destroy();
	unset($_SESSION);
}

include("include/header.php.inc");

if($logout=="yes")
{
	echo "         You are logged out. Please log in again to use Sessionweb\n";
}

if(!session_is_registered(myusername)){

	echo "         <form name=\"loginform\" method=\"post\" action=\"checklogin.php\">\n";
	echo "             <table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#FFFFFF\">\n";
	echo "                 <tr>\n";
	echo "                     <td colspan=\"3\"><strong>User Login </strong></td>\n";
	echo "                 </tr>\n";
	echo "                 <tr>\n";
	echo "                     <td width=\"78\">Username</td>\n";
	echo "                     <td width=\"294\"><input name=\"myusername\" type=\"text\" id=\"myusername\"></td>\n";
	echo "                 </tr>\n";
	echo "                 <tr>\n";
	echo "                     <td>Password</td>\n";
	echo "                     <td><input name=\"mypassword\" type=\"password\" id=\"mypassword\"></td>\n";
	echo "                 </tr>\n";
	echo "                 <tr>\n";
	echo "                     <td>&nbsp;</td>\n";
	echo "                     <td><input type=\"submit\" name=\"Submit\" value=\"Login\"></td>\n";
	echo "                 </tr>\n";
	echo "                 </table>\n";
	echo "         </form>\n";
}
else
{
	echo "         Welcome to sessionweb ".$_SESSION['user']."<br> \n";


}

include("include/footer.php.inc");
?>
