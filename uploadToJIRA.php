<?php

use Core\Authentication;
use Core\CSVParser;
use Core\JIRA;
use Models\Worklog;

include "vendor/autoload.php";

include "exceptions/DataTypeException.php";
include "exceptions/JIRAException.php";

include "./lib/classes/Console.php";
include "./lib/classes/CSVParser.php";
include './lib/classes/JIRA.php';
include './lib/classes/Authentication.php';
include "./lib/classes/DataType.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
/*
 * Initial script startup setting JIRA Login, URL, CSV Data.
 */
$formattedRows = CSVParser::getInstance()->load('./data/Timing Export.csv')->format()->getFormattedRows();
Authentication::setUsernameAndPassword(getenv('JIRA_USERNAME'), base64_decode(getenv('JIRA_PASSWORD')));
JIRA::setURL(getenv('JIRA_URL'));

try {
    //Loop through each row on the CSV and upload the Data.
    foreach ($formattedRows as $formattedRow) {
        JIRA::addWorklog($formattedRow['Task Title'], new Worklog($formattedRow['Task Title'], JIRA::formatDate($formattedRow['Start Date']), (float)$formattedRow['Duration'], $formattedRow['Notes']));
    }
} catch (Exception $exception) {
    Console::log($exception->getMessage());
}
