<?php
/**
 * This file show how to get and set the title of requirements and bugs in the sessions.
 * To start to use this file with its functionality rename it to customfunctions.php.inc
 */

define('GOOGLECODEPROJECT', 'sessionweb');
error_reporting(0);

/**
 * Gets the title of a req
 * MANDATORY. Used by sessionweb to fetch the name of a linked requirement
 * @param $aRequirement id for requirement
 */
function getRequirementNameFromServer($aRequirement)
{
  //Add your own implementation to obtain the name of the req....
  return getIssueInformationFromGoogleCode($aRequirement);
}

/**
 * Gets the title of a bug
 * MANDATORY. Used by sessionweb to fetch the name of a linked bug
 * @param $aBug id for bug/issue
 */
function getBugNameFromServer($aBug)
{
    //Add your own implementation to obtain the name of the bug
    return getIssueInformationFromGoogleCode($aBug);
}

/**
 * Gets the title of a bug from a Google Code Project
 * Example implementation.
 * @param $id Google code issue id
 */
function getIssueInformationFromGoogleCode($id)
{
    $url = "http://code.google.com/feeds/issues/p/".GOOGLECODEPROJECT."/issues/$id/comments/full?prettyprint=true";
    $pageContent = file_get_contents($url);

    $reader = new XMLReader();
    $reader->XML($pageContent);

    while ($reader->read() && $reader->name !== 'title');
    $reader->read();
    $title = $reader->value;
    $reader->close();
    if ($title=="")
    {
        return "Could not find title for issue";
    }
    else
        return $title;
}

?>