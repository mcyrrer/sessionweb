<?php
include 'classes/sessionObject.php';

$so = new sessionObject(5141);
$so->setNotes("TESTNOTES");
$so->setCharter("TESTCHARTER");
$so->setDebrief_notes("THIS IS A TEST");
$so->setDebriefed(1);
$so->setDebriefedby("matgus");

$so->saveObjectToDb();

//$so->printObject();