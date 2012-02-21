<?php
define('NUMBER_OF_CHARS_FILTER', 1);
define('FILE_STOP_LIST_WORD_CLOUD', '../include/StoplistWordCloud.txt');

session_start();
if (!session_is_registered(myusername)) {
    header("location:../index.php");
}
include_once('../config/db.php.inc');
include_once ('../include/commonFunctions.php.inc');


$wordsCountArray = array();

if ($_GET['word'] != "" && $_SESSION['useradmin'] == 1) {
    addWordToStopList($_GET['word']);
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="latin-1"/>
    <title>Session word cloud</title>
    <link rel="stylesheet" href="../css/wordcloud.css">
    <script type="text/javascript" src="../js/jquery-1.4.4.js"></script>
    <script type="text/javascript" src="../js/sessionwebjquery.js"></script>
    <script type="text/javascript" src="../js/jquery.colorbox-min.js"></script>


</head>

<body>


<?php

$con =getMySqlConnection();

if ($_GET['sessionid'] != "") {
    $sessionid = $_GET['sessionid'];
    $versionId = getSessionVersionId($_GET['sessionid']);
    $sessionData = getSessionData($sessionid);

    //print_r($sessionData);
    $sessionNotes = $sessionData['notes'];
    $sessionCharter = $sessionData['charter'];
    $notesWordCount = countWords($sessionData['notes']);
    $charterWordCount = countWords($sessionData['charter']);

    $wordCount = array_merge($notesWordCount, $charterWordCount);
    if ($_SESSION['useradmin'] == 1 && !$_GET['edit'] == "yes") {
        echo "<a href='?sessionid=$sessionid&edit=yes' style='font-size: 10px' TARGET='blank'>[Edit blocked words]</a><br>";
    }
    if ($_SESSION['useradmin'] == 1 && $_GET['edit'] == "yes") {
        echo "<H2>Click on the word to add to black list.</H2><br>";
        echo "<div id='addedword'></div>";
    }
    //    print_r($wordCount);
    if (count($wordCount) != 0) {
        printTagCloud($wordCount, 30);
    }
}
if ($_GET['all'] != "") {
    $sql = "SELECT title, notes,charter FROM sessioninfo ";
    $sql .= "WHERE executed = 1  ";
    if ($_GET['tester'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND username = '" . urldecode($_GET['tester']) . "' ";
        }
    }
    if ($_GET['team'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND teamname = '" . urldecode($_GET['team']) . "' ";
        }
    }
    if ($_GET['sprint'] != null) {
        $sql .= "AND sprintname = '" . urldecode($_GET['sprint']) . "' ";
    }

    $resultSession = getSessionNotesAncCarters($sql);
    $num_rows = mysql_num_rows($resultSession);
    $wordCountArray = array();
    while ($row = mysql_fetch_array($resultSession)) {
        $sessionNotes = $row['notes'];
        $sessionCharter = $row['charter'];
        $wordCountArray = array_merge_recursive($wordCountArray, countWords($row['notes']));
        $wordCountArray = array_merge_recursive($wordCountArray, countWords($row['notes']));
    }

    if ($_SESSION['useradmin'] == 1 && !$_GET['edit'] == "yes") {
        echo "<a href='?all=yes&edit=yes' style='font-size: 10px' TARGET='blank'>[Edit blocked words]</a><br>";
    }
    if ($_SESSION['useradmin'] == 1 && $_GET['edit'] == "yes") {
        echo "<H2>Click on the word to add to black list.</H2><br>";
        echo "<div id='addedword' class='class'></div>";
    }
    $wordCountArraySummary = array();
    foreach ($wordCountArray as $key => $oneWordArray)
    {
        $cnt = 0;
        foreach ($oneWordArray as $oneWordCount)
        {
            $cnt = $cnt + $oneWordCount;
        }
        $wordCountArraySummary[$key] = $cnt;

    }

    if (count($wordCountArray) != 0) {
        printTagCloud($wordCountArraySummary, 100);
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


    $max_size = 40; // max font size in pixels
    $min_size = 8; // min font size in pixels

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
            if ($_GET[edit] == "yes" && $_SESSION['useradmin'] == 1) {
                echo "<p class='wordcloudword'/>$key</p>";
            }
            else
            {
                //                echo '<a href="" style="font-size: ' . $size . 'px" title="' . $value . ' occurrence with word ' . $key . '">' . $key . '</a> ';
                echo '<span href="" style="font-size: ' . $size . 'px" title="' . $value . ' occurrence with word ' . $key . '">' . $key . '</span> ';

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
    //     print_r($wordCountArray);

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
