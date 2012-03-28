<?php
function echoBugsEdit($versionid)
{
	$myBugsArray = "";
	$rowSessionBugs = "";

	if($versionid!="")
	{

		$con = getMySqlConnection();


		$rowSessionBugs = getSessionBugs($versionid);

		mysql_close($con);

		if($rowSessionBugs!="")
		{
			$myBugsArray = implode("\",\"", $rowSessionBugs);
		}
		echo "            <script type=\"text/javascript\">\n";
		if($myBugsArray!="")
		{
			echo "              var myBugs = new Array(\"$myBugsArray\");\n";
		}
		else
		{
			echo "              var myBugs = new Array();\n";
		}
		echo "            </script>\n";


		foreach($rowSessionBugs as $row)
		{
			$bug = $row;
			echo "      <div id=\"bugdiv_$bug\">\n";
			echo "            <table width=\"*\" border=\"0\">\n";
			echo "                        <tr>\n";
			echo "                              <td>\n";
			echo "                                   <a href=\"".$_SESSION['settings']['url_to_rms']."$bug\" class=\"bugurl\" target=\"_blank\">$bug</a>\n";
			echo "                              </td>\n";
			echo "                              <td>\n";
			echo "                                    <div id=\"bug_$bug\">\n";
			echo "                                          <img src=\"pictures/removeicon.png\" alt=\"[remove]\"></div>\n";
			echo "                                    </td>\n";
			echo "                              </tr>\n";
			echo "                  </table>\n";
			echo "      </div>\n";
			echo "          <script type=\"text/javascript\">\n";
			echo "                      var bugValue = \"".$bug."\"\n";
			echo "                      $(\"#bug_$bug\").click(function(){\n";
			echo "                          var thisIe = this.id;\n";
			echo "                          var bugUrlId = \"bugdiv_\" +\n";
			echo "                          bugValue;\n";
			echo "                          if (this.id != bugUrlId) {\n";
			echo "                              var answer = confirm(\"Remove bug $bug?\")\n";
			echo "                              if (answer) {\n";
			echo "                                  $(\"#bugdiv_$bug\").remove();\n";
			echo "                                  bugPos = jQuery.inArray(\"$bug\", myBugs);\n";
			echo "                                  if (bugPos != -1) {\n";
			echo "                                      var removedelements = myBugs.splice(bugPos, 1);\n";
			echo "                                      $('#buglist_hidden').text(myBugs.toString());\n";
			echo "                                  }\n";
			echo "                              }\n";
			echo "                          }\n";
			echo "                      });\n";
			echo "          </script>\n";
		}

	}
	else
	{
		echo "            <script type=\"text/javascript\">\n";
		echo "              var myBugs = new Array();\n";
		echo "            </script>\n";
	}
}

function echoRequirementsEdit($versionid)
{
	$myRequirementsArray = "";
	$rowSessionRequirements = "";
	if($versionid!="")
	{
		$con = getMySqlConnection();


		$rowSessionRequirements = getSessionRequirements($versionid);

		mysql_close($con);

		if($rowSessionRequirements!="")
		{
			$myRequirementsArray = implode("\",\"", $rowSessionRequirements);
		}
		echo "            <script type=\"text/javascript\">\n";
		if($myRequirementsArray!="")
		{
			echo "              var myRequirements = new Array(\"$myRequirementsArray\");\n";

		}
		else
		{
			echo "              var myRequirements = new Array();\n";
		}
		echo "            </script>\n";


		foreach($rowSessionRequirements as $row)
		{
			$requirement = $row;
			echo "      <div id=\"requirementdiv_$requirement\">\n";
			echo "            <table width=\"*\" border=\"0\">\n";
			echo "                        <tr>\n";
			echo "                              <td>\n";
			echo "                                   <a href=\"".$_SESSION['settings']['url_to_rms']."$requirement\" class=\"requirementurl\" target=\"_blank\">$requirement</a>\n";
			echo "                              </td>\n";
			echo "                              <td>\n";
			echo "                                    <div id=\"requirement_$requirement\">\n";
			echo "                                          <img src=\"pictures/removeicon.png\" alt=\"[remove]\"></div>\n";
			echo "                                    </td>\n";
			echo "                              </tr>\n";
			echo "                  </table>\n";
			echo "      </div>\n";
			echo "          <script type=\"text/javascript\">\n";
			echo "                      var requirementValue = \"".$requirement."\"\n";
			echo "                      $(\"#requirement_$requirement\").click(function(){\n";
			echo "                          var thisIe = this.id;\n";
			echo "                          var requirementUrlId = \"requirementdiv_\" +\n";
			echo "                          requirementValue;\n";
			echo "                          if (this.id != requirementUrlId) {\n";
			echo "                              var answer = confirm(\"Remove requirement $requirement?\")\n";
			echo "                              if (answer) {\n";
			echo "                                  $(\"#requirementdiv_$requirement\").remove();\n";
			echo "                                  requirementPos = jQuery.inArray(\"$requirement\", myRequirements);\n";
			echo "                                  if (requirementPos != -1) {\n";
			echo "                                      var removedelements = myRequirements.splice(requirementPos, 1);\n";
			echo "                                      $('#requirementlist_hidden').text(myRequirements.toString());\n";
			echo "                                  }\n";
			echo "                              }\n";
			echo "                          }\n";
			echo "                      });\n";
			echo "          </script>\n";
		}

	}
	else
	{
		echo "            <script type=\"text/javascript\">\n";
		echo "              var myRequirements = new Array();\n";
		echo "            </script>\n";
	}
}


function echoSessionlinkEdit($versionid)
{
    
	$mySessionlinkArray = "";
	$rowSessionSessionlink = "";
	if($versionid!="")
	{
		$con = getMySqlConnection();


		$rowSessionSessionlink = getConnectedSessions($versionid);

//		mysql_close($con);

		if($rowSessionSessionlink!="")
		{
			$mySessionlinkArray = implode("\",\"", $rowSessionSessionlink);
		}
		echo "            <script type=\"text/javascript\">\n";
		if($mySessionlinkArray!="")
		{
			echo "              var mySessionlinks = new Array(\"$mySessionlinkArray\");\n";
		}
		else
		{
			echo "              var mySessionlinks = new Array();\n";
		}
		echo "            </script>\n";

		if($rowSessionSessionlink!="")
		{
			foreach($rowSessionSessionlink as $row)
			{
//				$versionid = $row;
                $versionid = GetVersionidFromSessionid($row);
				$aSessionTitle = GetSessionTitle($versionid);
				$sessionlink = GetSessionIdFromVersionId($versionid);
				
				echo "      <div id=\"sessionlinkdiv_$sessionlink\">\n";
				echo "            <table width=\"*\" border=\"0\">\n";
				echo "                        <tr>\n";
				echo "                              <td>\n";
				echo "                                   #$sessionlink <a href=\"session.php?sessionid=$aSessionToLink&command=view\" class=\"sessionlinkurl\" target=\"_blank\"> $aSessionTitle</a>\n";
				echo "                              </td>\n";
				echo "                              <td>\n";
				echo "                                    <div id=\"sessionlink_$sessionlink\">\n";
				echo "                                          <img src=\"pictures/removeicon.png\" alt=\"[remove]\"></div>\n";
				echo "                                    </td>\n";
				echo "                              </tr>\n";
				echo "                  </table>\n";
				echo "      </div>\n";
				echo "          <script type=\"text/javascript\">\n";
				echo "                      var sessionlinkValue = \"".$sessionlink."\"\n";
				echo "                      $(\"#sessionlink_$sessionlink\").click(function(){\n";
				echo "                          var thisIe = this.id;\n";
				echo "                          var sessionlinkUrlId = \"sessionlinkdiv_\" + sessionlinkValue;\n";
				echo "                          if (this.id != sessionlinkUrlId) {\n";
				echo "                              var answer = confirm(\"Remove sessionlink $sessionlink?\")\n";
				echo "                              if (answer) {\n";
				echo "                                  $(\"#sessionlinkdiv_$sessionlink\").remove();\n";
				echo "                                  sessionlinkPos = jQuery.inArray(\"$sessionlink\", mySessionlinks);\n";
				echo "                                  if (sessionlinkPos != -1) {\n";
				echo "                                      var removedelements = mySessionlinks.splice(sessionlinkPos, 1);\n";
				echo "                                      $('#sessionlinklist_hidden').text(mySessionlinks.toString());\n";
				echo "                                  }\n";
				echo "                              }\n";
				echo "                          }\n";
				echo "                      });\n";
				echo "          </script>\n";
			}
		}

	}
	else
	{
		echo "            <script type=\"text/javascript\">\n";
		echo "              var mySessionlink = new Array();\n";
		echo "            </script>\n";
	}
}

?>