<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';

echoSessionForm();

include("include/footer.php.inc");

function echoSessionForm()
{
	echo "<form action=\"session.php\" method=\"POST\" accept-charset=\"utf-8\">\n";

	echo "<table width=\"1024\" border=\"1\">\n";
	echo "      <tr>\n";
	echo "            <td>\n";
	echo "                  <table width=\"1024\" border=\"0\">\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <h1>New Session</h1>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <img src=\"pictures/line.png\" alt=\"line\">\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <h3>Setup</h3>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Session title: </td>\n";
	echo "                              <td><input type=\"text\" size=\"133\" value=\"\" name=\"title\"></td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Tester: </td>\n";
	echo "                              <td>\n";
	echoTesterSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Team: </td>\n";
	echo "                              <td>\n";
	echoTeamSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Sprint: </td>\n";
	echo "                              <td>\n";
	echoSprintSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Mission: </td>\n";
	echo "                              <td>\n";
	echo "                                  <textarea id=\"textarea1\" name=\"mission\"  rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">\n";
	echo "                                  </textarea>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                  <input type=\"submit\" value=\"Save\"/>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <p><img src=\"pictures/line.png\" alt=\"line\"></p>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <h3>Execution</h3>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Notes: </td>\n";
	echo "                              <td>\n";
	echo "                                  <textarea id=\"textarea2\" name=\"notes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">\n";
	echo "                                  </textarea>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Metrics: </td>\n";
	echo "                              <td>\n";
	echo "                                    <table width=\"1024\" border=\"0\">\n";
	echo "                                          <tr>\n";
	echo "                                                <td>Setup(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"setuppercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Test(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"testpercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Bug(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"bugpercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Oppertunity(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"oppertunitypercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Session duration (min): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"tester\">\n";
	echoDurationSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                          </tr>\n";
	echo "                                    </table>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Executed:</td>\n";
	echo "                              <td>\n";
	echo "                                  <input type=\"checkbox\" name=\"executed\" value=\"yes\" checked=\"checked\">\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                  <input type=\"submit\" value=\"Save\"/>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                  </table>\n";
	echo "            </td>\n";
	echo "      </tr>\n";
	echo "</table>\n";

	echo "</form>\n";
}

function echoPercentSelection()
{
	for ($index  = 0; $index  <= 100; $index = $index + 5) {
		echo "                                      <option>$index</option>";
	}
}

function echoDurationSelection()
{
	for ($index  = 15; $index  <= 480; $index = $index + 15) {
		echo "                                      <option>$index</option>";
	}
}