<?php
define('NUMBER_OF_CHARS_FILTER', 1);
define('FILE_STOP_LIST_WORD_CLOUD', '../include/StoplistWordCloud.txt');

session_start();
require_once('../include/validatesession.inc');

require_once('../config/db.php.inc');
require_once ('../include/commonFunctions.php.inc');
require_once('../include/db.php');

$wordsCountArray = array();

if (isset($_GET['word']) && $_GET['word'] != "" && $_SESSION['useradmin'] == 1) {
    addWordToStopList($_GET['word']);
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <title>Session word cloud</title>
    <link rel="stylesheet" href="../css/sessionwebcss.css">
    <script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../js/sessionwebjquery-v27.js"></script>
    <script type="text/javascript" src="../js/jquery.colorbox-min.js"></script>


</head>

<body>

<?php
echo '<div id="wordcloudbg">';

if (isset($_GET['sessionid']) && $_GET['sessionid'] != "")
    printCloud();
elseif (isset($_REQUEST['tester']) || isset($_REQUEST['team']) || isset($_REQUEST['sprint'])) {
    //$con = getMySqlConnection();
    printCloud();
    //mysql_close($con);
}
else
{
    if (isset($_REQUEST['edit'])) {
        echo '<form method="post" action="wordcloud.php?all=true&edit=yes">';

    }
    else

        echo '<form method="post" action="wordcloud.php?all=true">';

    echo "Tester:";
    if ($_SESSION['useradmin'] == 1) {
        echoTesterFullNameSelect("");
    }
    else
    {
        echo "<select id='select_tester' name='tester'> \n";
        echo "        <option></option>\n";
        echo "        <option>" . $_SESSION['username'] . "</option> \n";
        echo "</select>\n";

    }

    if ($_SESSION['useradmin'] == 1) {
        echo "Team:";
        echoTeamSelect("",true);
    }

    echo "Sprint:";
    echoSprintSelect("",true);


    echo '<input type="submit" name="Submit" value="Generate report">';
}
echo "</div>";
function printCloud()
{
    $con = getMySqlConnection();

    if (isset($_GET['sessionid']) && $_GET['sessionid'] != "") {
        $sessionid = $_GET['sessionid'];
        $versionId = getSessionVersionId($_GET['sessionid']);
        $sessionData = getSessionData($sessionid);

        //print_r($sessionData);
        $sessionNotes = $sessionData['notes'];
        $sessionCharter = $sessionData['charter'];
        $notesWordCount = countWords($sessionData['notes']);
        $charterWordCount = countWords($sessionData['charter']);

        $wordCount = array_merge($notesWordCount, $charterWordCount);
        if ($_SESSION['useradmin'] == 1 && isset($_GET['edit']) && !$_GET['edit'] == "yes") {
            echo "<a href='?sessionid=$sessionid&edit=yes' style='font-size: 10px' TARGET='blank'>[Edit blocked words]</a><br>";
        }
        if ($_SESSION['useradmin'] == 1 && isset($_GET['edit']) && $_GET['edit'] == "yes") {
            echo "<H2>Click on the word to add to black list.</H2><br>";
            echo "<div id='addedword'></div>";
        }
        //    print_r($wordCount);
        if (count($wordCount) != 0) {
            printTagCloud($wordCount, 30);
        }
    }
    if (isset($_GET['all']) && $_GET['all'] != "") {
        $sql = "SELECT title, notes,charter FROM sessioninfo ";
        $sql .= "WHERE executed = 1  ";
        if ($_REQUEST['tester'] && strcmp($_REQUEST['tester'],'') != 0) {
            if ($_SESSION['useradmin'] == 1) {
                $sql .= "AND username = '" . urldecode($_REQUEST['tester']) . "' ";
            }
        }
        if (isset($_REQUEST['team']) && strcmp($_REQUEST['team'],'') != 0) {
            if ($_SESSION['useradmin'] == 1) {
                $sql .= "AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
            }
        }
        if (isset($_REQUEST['sprint']) && strcmp($_REQUEST['sprint'],'') != 0) {
            $sql .= "AND sprintname = '" . urldecode($_REQUEST['sprint']) . "' ";
        }
        //echo $sql;

        $resultSession = getSessionNotesAncCarters($sql);
        $num_rows = mysql_num_rows($resultSession);
        $wordCountArray = array();
        while ($row = mysql_fetch_array($resultSession)) {
            $sessionNotes = $row['notes'];
            $sessionCharter = $row['charter'];
            $wordCountArray = array_merge_recursive($wordCountArray, countWords($sessionNotes));
            $wordCountArray = array_merge_recursive($wordCountArray, countWords($sessionCharter));
        }

        if ($_SESSION['useradmin'] == 1 && isset($_GET['edit']) && !$_GET['edit'] == "yes") {
            echo "<a href='?all=yes&edit=yes' style='font-size: 10px' TARGET='blank'>[Edit blocked words]</a><br>";
        }
        if ($_SESSION['useradmin'] == 1 && isset($_GET['edit']) && $_GET['edit'] == "yes") {
            echo "<H2>Click on the word to add to black list.</H2><br>";
            echo "<div id='addedword' class='class'></div>";
        }
        $wordCountArraySummary = array();
        //echo count($wordCountArray);
        foreach ($wordCountArray as $key => $oneWordArray)
        {
            //echo "_START_<br>Key:" . $key;
            //print_r($oneWordArray);
            //echo "<br>_END_<br>";
            $cnt = 1;
            if (is_array($oneWordArray))
                foreach ($oneWordArray as $oneWordCount)
                {
                    $cnt = $cnt + $oneWordCount;
                }
            $wordCountArraySummary[$key] = $cnt;
            // echo  "$key : $cnt\n";

        }
        if (count($wordCountArray) != 0) {
            printTagCloud($wordCountArraySummary, 100);
            //print_r($wordCountArraySummary);
        }

    }
}

?>
</body>

</html>

<?php

function addWordToStopList($addword)
{
    $stopList = getStopWordList();
    if (!in_array($addword, $stopList)) {
        //echo "$addword<br>";
        $fh = fopen(FILE_STOP_LIST_WORD_CLOUD, 'a') or die("can't open file");
        $wordToAdd = $addword . "|";
        fwrite($fh, $wordToAdd);
        fclose($fh);
        //echo "$addword<br>";
        echo "0 - $addword added to black list.";
    }
    else
    {
        echo "1 - word already exist";
    }
    exit();
}

function getStopWordList()
{
    $stopListWordCloud = array();
    $f = fopen(FILE_STOP_LIST_WORD_CLOUD, "r");
    while ($line = fgets($f)) {

        $explodedLine = explode('|', $line);

        foreach ($explodedLine as $word)
        {
            $stopListWordCloud[] = strtolower($word);
        }
    }
    return $stopListWordCloud;
}

function printTagCloud($tags, $maxItemToDisplay)
{

    arsort($tags);
    $tags = array_slice($tags, 0, $maxItemToDisplay);
    // $tags is the array
    ksort($tags);


    $max_size = 80; // max font size in pixels
    $min_size = 10; // min font size in pixels

    // largest and smallest array values
    $max_qty = max(array_values($tags));
    $min_qty = min(array_values($tags));

    // find the range of values
    $spread = $max_qty - $min_qty;
    if ($spread == 0) { // we don't want to divide by zero
        $spread = 1;
    }

    // set the font-size increment
    $step = ($max_size - $min_size) / ($spread);
    // loop through the tag array
    foreach ($tags as $key => $value) {
        // calculate font-size
        // find the $value in excess of $min_qty
        // multiply by the font-size increment ($size)
        // and add the $min_size set above
        $size = round($min_size + (($value - $min_qty) * $step));

        if (htmlspecialchars($key) != "&nbsp;") {
            if (isset($_GET['edit']) && $_GET['edit'] == "yes" && $_SESSION['useradmin'] == 1) {
                echo "<p class='wordcloudword'/>$key</p>";
            }
            else
            {
                //                echo '<a href="" style="font-size: ' . $size . 'px" title="' . $value . ' occurrence with word ' . $key . '">' . $key . '</a> ';
                if ($_SESSION['useradmin'] == 1 && isset($_GET['edit']) && $_GET['edit'] == "yes")
                    echo '<span href="" style="font-size: ' . $size . 'px" title="' . $value . ' occurrence with word ' . $key . '">' . $key . '</span>';
                else
                    echo '<span href="" style="font-size: ' . $size . 'px" title="' . $value . ' occurrence with word ' . $key . '"><a href="../list.php?searchstring=' . $key . '" target="_top">' . $key . '</a></span> ';


            }
        }

    }

}

/**
 * @param  $words Text line to process
 * @param  $wordCountArray
 * @return Number of each word stored in an array
 */
function countWords($words, $wordCountArray = array())
{
    $stopWords = getStopWordList();
    $lineToProcess = prepareLine($words);
    $wordsArray = explode(" ", $lineToProcess);
    //    print_r($stopWords);
    //    print_r($wordsArray);
    foreach ($wordsArray as $word)
    {

        if (!strlen($word) < NUMBER_OF_CHARS_FILTER && !is_numeric($word)) {


            if (!in_array($word, $stopWords)) {
                if (array_key_exists($word, $wordCountArray)) {
                    $wordCountArray[$word] = $wordCountArray[$word] + 1;
                }
                else
                {
                    $wordCountArray[$word] = 1;
                }
            }
            else
            {
                //echo "!$word! not added<br>";
            }
        }
    }
    //print_r($wordCountArray);

    return $wordCountArray;


}

function prepareLine($lineToProcess)
{
    $lineToProcess = preg_replace("/<.*?>/", "", $lineToProcess);
    $lineToProcess = str_replace("&nbsp;", "", $lineToProcess); //Will remove the Non-breaking space chars before we decode the html..
    $lineToProcess = htmlspecialchars_decode($lineToProcess);
    $lineToProcess = urldecode($lineToProcess);
    $lineToProcess = removeNewLineAndCaretReturn($lineToProcess);
    $lineToProcess = stripEndAndStartCharsFromWord($lineToProcess);
    $lineToProcess = strtolower($lineToProcess);
    return $lineToProcess;
}

/**
 * Will replace \r and \n with a blank char.
 * @param  $lineToProcess Line to process
 * @return The line without \r and  \n
 */
function removeNewLineAndCaretReturn($lineToProcess)
{
    $lineToProcess = str_replace("\r\n", " ", $lineToProcess);
    $lineToProcess = str_replace("\n\r", " ", $lineToProcess);
    $lineToProcess = str_replace("\n", " ", $lineToProcess);
    $lineToProcess = str_replace("\r", " ", $lineToProcess);
    $lineToProcess = str_replace("\t", " ", $lineToProcess);
    $lineToProcess = str_replace("\t", " ", $lineToProcess);
    return $lineToProcess;
}

function stripEndAndStartCharsFromWord($lineToProcess)
{
    $lineToProcess = str_replace(",", "", $lineToProcess);
    $lineToProcess = str_replace(".", "", $lineToProcess);
    $lineToProcess = str_replace("'", "", $lineToProcess);
    $lineToProcess = str_replace("]", "", $lineToProcess);
    $lineToProcess = str_replace(":", "", $lineToProcess);
    $lineToProcess = str_replace(";", "", $lineToProcess);

    $lineToProcess = str_replace(")", "", $lineToProcess);
    $lineToProcess = str_replace("\"", "", $lineToProcess);
    $lineToProcess = str_replace("!", "", $lineToProcess);
    $lineToProcess = str_replace("?", "", $lineToProcess);
    $lineToProcess = str_replace("(", "", $lineToProcess);
    $lineToProcess = str_replace("[", "", $lineToProcess);
    $lineToProcess = str_replace("\"", "", $lineToProcess);
    $lineToProcess = str_replace("'", "", $lineToProcess);
    $lineToProcess = str_replace("*", "", $lineToProcess);
    $lineToProcess = str_replace("-", "", $lineToProcess);
    $lineToProcess = str_replace("#", "", $lineToProcess);
    $lineToProcess = str_replace("|", "", $lineToProcess);
    $lineToProcess = str_replace("U+00A0", "", $lineToProcess);


    return $lineToProcess;
}

?>