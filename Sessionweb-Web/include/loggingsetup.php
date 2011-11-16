<?php
$logpath = '';
if (file_exists('include/log4php/Logger.php')) {
    require_once ('include/log4php/Logger.php');
    $logpath = 'log/sessionweb.log';
}
elseif (file_exists('../include/log4php/Logger.php'))
{
    require_once ('../include/log4php/Logger.php');
    $logpath = '../log/sessionweb.log';
}
elseif (file_exists('../../include/log4php/Logger.php'))
{
    require_once ('../../include/log4php/Logger.php');
    $logpath = '../../log/sessionweb.log';
}
elseif (file_exists('../../../include/log4php/Logger.php'))
{
    require_once ('../../../include/log4php/Logger.php');
    $logpath = '../../../log/sessionweb.log';
}
elseif (file_exists('../../../../include/log4php/Logger.php'))
{
    require_once ('../../../../include/log4php/Logger.php');
    $logpath = '../../../../log/sessionweb.log';
}
else
{
    echo "Log4Php not found. pleas correct this in the include/loggingsetup.php.";
    exit();
}

$logger = Logger::getRootLogger();
$logger->setLevel(LoggerLevel::DEBUG);

$appender = new LoggerAppenderFile("MyAppender");

$appender->setFile("$logpath", true);
$appenderlayout = new LoggerLayoutTTCC();
$appender->setLayout($appenderlayout);
$appender->activateOptions();

$logger->removeAllAppenders();
$logger->addAppender($appender);

?>