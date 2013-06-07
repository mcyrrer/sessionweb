<?php

//-------------------------------------------------------------------
// Name:		minVersion.php
// Desc:		Determines minimum PHP function to run script
// Author:		Alex Roxon (http://phpduck.com/)
// Version:		1.0.0
//-------------------------------------------------------------------
// Copyright 2010 Alex Roxon, PHPDuck.com
// All Rights Reserved
//-------------------------------------------------------------------

// So the script doesn't time out on large operations
set_time_limit( 300 );

// For page execution time
$Start = microtime(true);

// Some definitions
DEFINE("XML_URL", "functions.xml"); // Set to http://phpduck.com/resources/functions.xml to retrieve latest functions.
DEFINE("FILENAME", "minVersion.php"); // Current filename.

// Filetypes to check
$FileTypes = array( 'inc','php', 'php3', 'php4', 'php5', 'phtml' );

// DO NOT EDIT BEYOND THIS POINT!

// Files array
$Files = array();

// Found functions
$Found = array();

// Current highest
$Highest = 0;

// Retrieve the xml file
$XML = simplexml_load_file( XML_URL );

// Sort in to an array
$Functions = get_object_vars( $XML );

// Retrieve list of PHP files
RetrieveFiles();

// Let's loop through the files
foreach( $Files as $File ) {
	if( file_exists( $File ) ) {
		$Contents = file_get_contents( $File );

		// Let's grab the PHP code
		while( preg_match( '/\<\?php/i', $Contents ) === 1 ) {
			$Code = substr( $Contents, stripos( $Contents, '<?php' ), strlen( $Contents ) );
			$Code = substr( $Code, 0, stripos( $Code, '?>' ) + 2 );
			
			// Let's test the functions
			preg_match_all( "/([a-z0-9\_]+)\(.*?\;/", $Code, $Funcs1 );
			preg_match_all( "/([a-z0-9\_]+[\s])\(.*?\;/", $Code, $Funcs2 );
			$Funcs = array_merge( $Funcs1[1], $Funcs2[1] );

			// Let's check if we have the function in our xml files.
			foreach( $Funcs as $Function ) {
				if( isset( $Found[ $Function ] ) ) {
					$Found[ $Function ][ 'occurences' ]++;
				} else if ( isset( $Functions[ $Function ] ) ) {
					if( is_numeric( $Functions[ $Function ] ) ) {
						$Found[ $Function ] = array(
							'occurences'	=> 1,
							'version'		=> $Functions[ $Function ],
						);
						
						// Ammend the highest current version?
						if( (float) $Functions[ $Function ] > (float) $Highest ) {
							$Highest = (float) $Functions[ $Function ];
						}
					} else {
						$Found[ $Function ] = array(
							'occurences'	=> 1,
							'extension'		=> $Functions[ $Function ],
						);
					}
				}
			}

			// Replace the code and continue
			$Contents = str_replace( $Code, null, $Contents);
		}
	}
}

// Retrieve files function
function RetrieveFiles( $Dir = "." ) {
	global $FileTypes;
	global $Files;

	// Open the directory
	if ( $handle = opendir( $Dir ) ) {
		while ( ($file = readdir( $handle ) ) !== false ) {

			// If we have a proper files
			if ( $file != "." && $file != ".." ) {
				
				if( $Dir == '.' ) $Dir = null;
				$FileType = substr( $file, strrpos( $file, "." ) + 1, strlen( $file ) );

				// If we have a php files
				if( in_array( strtolower( $FileType ), $FileTypes ) && ( strtolower( $file ) != strtolower( FILENAME ) ) ) {
					$Files[] = $Dir . $file;
				} elseif ( filetype( $Dir . $file ) == 'dir' ) {
					$Folder = $Dir . $file;
					if( substr( $Folder, -1 ) !== '/' ) $Folder .= '/';

					// If we've found a child folder, let's retrieve the files in there.
					RetrieveFiles( $Folder );
				}
			}
		}
	}
    closedir($handle);
}

// Format results function
function Format_Results() {
	global $Found;

	$Return = null;
	foreach( $Found as $Function => $Vars) {
		$Return .= '<a href="http://php.net/' . $Function . '" target="_new">' . $Function . '</a> (' . $Vars['occurences'] . ' occurences) - ';
		if( isset( $Vars['version'] ) ) {
			$Return .= 'PHP ' . $Vars['version'];
		} elseif( isset( $Vars['extension'] ) ) {
			$Return .= $Vars['extension'];
		}
		$Return .= "<br />";
	}
	return $Return;
}

// Output extensions
function Output_Extensions() {
	global $Found;

	$Done = array();
	
	$Return = null;
	foreach( $Found as $Function => $Vars) {
		if( isset( $Vars['extension'] ) && ! in_array( $Vars['extension'], $Done ) ) {
			$Return .= '<a href="http://php.net/' . $Function . '" target="_new">' . $Function . "</a> - " .$Vars['extension'] . "<br />";
			$Done[] = $Vars['extension'];
		}
	}
	if( $Return != null ) {
		$Return = "<div style='font-size: 13px; margin-top: -5px; margin-bottom: 20px;'>\n<h2>PHP Extensions required:</h2>\n" . $Return . "</div>";
	}

	return $Return;
}

$End = microtime(true);
$Execution = $End - $Start;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<style type="text/css">
h2 {
	font-size: 16px;
	font-family: arial;
	margin: 0;
}
div {
	font-size: 11px;
}
div a {
	color: #446b84;
	text-decoration: none;
	border-bottom: 1px #446b84 dotted;
}
div a:hover {
	text-decoration: underline;
	border: 0;
}

#results {
	margin-top: 8px;
}

#results a {
	color: red;
	text-decoration: none;
	border: 0;
}

</style>
<script type="text/javascript">
var Show = false;

function Toggle() {
	if( Show == false ) {
		document.getElementById('dis').style.height = 'auto';
		document.getElementById('show').innerHTML = 'Hide Results';
		Show = true;
	} else {
		document.getElementById('dis').style.height = '16px';
		document.getElementById('show').innerHTML = 'Show Results';
		Show = false;
	}
	return false;
}
</script>
<title> minVersion.php Result - PHPDuck.com </title>
<meta name="Author" content="PHPDuck.com">
</head>
<body style="font-size: 15px; font-family: arial;">
	<h1 style="font-family: arial; font-size: 18px;">PHPDuck.com minVersion.php Results</h1>
	<div style='font-size: 11px;'>Results took <?php echo round($Execution, 3); ?> seconds to render.</div>
	<table><tr><td>The minimum PHP version you need installed to run the PHP files in this directory is:</td>
	<td style='float: left; font-weight: bold; font-size: 40px; color: green;'><?php echo $Highest; ?></td></tr></table>
	<br />
	
	<?php echo Output_Extensions(); ?>

	<div style='width: 700px; border: 1px #8a979f solid; background-color: #e3e5e6; height: 16px; padding: 4px; overflow: hidden;' id='dis'>
		<a href="#" id="show" onclick='return Toggle();'>Show Results</a>
		<div id="results">
			<?php echo Format_Results(); ?>
		</div>
	</div>
	<br /><br />
	<div style='font-size: 10px;'>&copy; Copyright 2010 <a href="http://phpduck.com/">PHP Duck</a>. All Rights Reserved.</div>
</body>
</html>
