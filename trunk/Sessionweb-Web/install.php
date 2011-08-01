<?php
if($_REQUEST[adm_user]!="")
{

    $adm_user = $_REQUEST[adm_user];
    $adm_password= $_REQUEST[adm_password];
    $user_name =  $_REQUEST[user_name];
    $user_password = $_REQUEST[user_password];

    //Check that adm user and password is correct. 
    $con = mysql_connect('localhost', $adm_user ,$adm_password) or die("Can not connect to Mysql db, please check your user and password");
    mysql_close($con);
    
    $loadDbWithSessionWebSql = "mysql -u$adm_user -p$adm_password < config/SessionwebDbLayout_latest.sql";

    exec($loadDbWithSessionWebSql, $result);

    echo "Database sessionwebos created<br>";

    $createDatabaseSql ="mysql -u$adm_user -p$adm_password -e \"CREATE USER '$user_name'@'localhost' IDENTIFIED BY '$user_password';\" ";
    exec($createDatabaseSql, $result);
    echo "user $user_name created<br>";


    $setupGranSqlStep1  = "mysql -u$adm_user -p$adm_password -e \"GRANT USAGE ON * . * TO  '$user_name'@'localhost' IDENTIFIED BY  '$user_password' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;;\" ";
    exec($setupGranSqlStep1, $result);

    $setupGranSqlStep2  = "mysql -u$adm_user -p$adm_password -e \"GRANT SELECT , INSERT , UPDATE , DELETE ON  `sessionwebos` . * TO  '$user_name'@'localhost';;\" ";
    exec($setupGranSqlStep2, $result);

    echo "Access rights to sessionwebos for user $user_name is set to SELECT , INSERT , UPDATE , DELETE<br>";

    $configfileString = "<?php
        define('DB_HOST_SESSIONWEB', 'localhost');
        define('DB_USER_SESSIONWEB', '$user_name');
        define('DB_PASS_SESSIONWEB', '$user_password');
        define('DB_NAME_SESSIONWEB', 'sessionwebos');
        ?>"; 
    $myFile = "config/db.php.inc";
    $fh = fopen($myFile, 'w');
    fwrite($fh,
    $configfileString);
    fclose($fh);
    echo "<br>Installation finished. Please <b>remove</b> this file (install.php) and go to <a href=\"index.php\">Sessionweb</a> and use username <b>admin</b> and password <b>admin</b>.";
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Install Sessionweb</title>
<link rel="stylesheet" type="text/css" href="css/install.css"
	media="all">

</head>
<body id="main_body">
<div id="form_container">
<h1><a>Install Sessionweb</a></h1>
<form id="form_92890" class="appnitro" method="post" action="">
<div class="form_description">
<h2>Install Sessionweb</h2>
<p>Add information below to automaticly create a MySql database and load
it with Sessionweb tables on this server <br>For installation on a linux/unix server please make sure that file
conf/db.php.inc is possible to write to for the web server user. If not
change file permission by executing <b>chmod 666 conf/db.php.inc</b>

</p>
<p>If you would like to create the database by yourselfe please edit
conf/db.php.inc and import latest version of
conf/mysql_sessionweb_layout.x.x.sql to your database <br> Please note
if you would like to change the database name you will need to edit the
databasename mysql_sessionweb_layout.x.x.sql 

</p>
<p>If sessionweb database already is in place and conf/db.php.inc is up
to date please remove install.php to start to use sessionweb. Default user created is admin with password admin.</p>
</div>
<ul>
	<!--	<li id="li_1"><label class="description" for="element_1"> MySql host </label>-->
	<!--	<div><input id="element_1" name="mysqlhost" class="element text medium"-->
	<!--		type="text" maxlength="255" value="" /></div>-->
	<!--	</li>-->
	<li id="li_2"><label class="description" for="element_2"> MySql user
	with administration rights (e.g. root) </label>
	<div><input id="element_2" name="adm_user" class="element text medium"
		type="text" maxlength="255" value="" /></div>
	</li>
	<li id="li_3"><label class="description" for="element_3"> MySql
	password for user above </label>
	<div><input id="element_3" name="adm_password"
		class="element text medium" type="text" maxlength="255" value="" /></div>
	</li>
	<!--                    <li id="li_4">-->
	<!--                        <label class="description" for="element_4">-->
	<!--                            Name of -->
	<!--                            database to use (e.g. sessionweb) -->
	<!--                        </label>-->
	<!--                        <div>-->
	<!--                            <input id="element_4" name="db_name" class="element text medium" type="text" maxlength="255" value="" />-->
	<!--                        </div>-->
	<!--                    </li>-->
	<li id="li_5"><label class="description" for="element_5"> Mysql
	sessionweb user (e.g. sessionweb) - will be created </label>
	<div><input id="element_5" name="user_name" class="element text medium"
		type="text" maxlength="255" value="" /></div>
	</li>
	<li id="li_6"><label class="description" for="element_6"> MySql
	password for user above </label>
	<div><input id="element_6" name="user_password"
		class="element text medium" type="text" maxlength="255" value="" /></div>
	</li>
	<li class="buttons"><input type="hidden" name="form_id" value="92890" /><input
		id="saveForm" class="button_text" type="submit" name="submit"
		value="Install" /></li>
</ul>
</form>
</div>
</body>
</html>
