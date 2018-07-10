<?php

use Core\Authentication;
use Core\CSVParser;
use Core\JIRA;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Models\Worklog;

include "vendor/autoload.php";

include "exceptions/DataTypeException.php";
include "exceptions/JIRAException.php";

include "./lib/classes/Console.php";
include "./lib/classes/CSVParser.php";
include "./lib/classes/JIRA.php";
include "./lib/classes/Authentication.php";
include "./lib/classes/DataType.php";

$formattedRows = CSVParser::getInstance()->load('./data/Timing Export.csv')->format()->getFormattedRows();

//Sets the Authentication for JIRA.
Authentication::setAuthenticationFromFile('credentials.json');
//Sets the URL for the JIRA instance.
JIRA::setURL('');

try {
    //Loop through each row on the CSV and upload the Data.
    foreach ($formattedRows as $formattedRow) {
        JIRA::addWorklog($formattedRow['Task Title'], new Worklog($formattedRow['Task Title'], JIRA::formatDate($formattedRow['Start Date']), (float)$formattedRow['Duration'], $formattedRow['Notes']));
    }
} catch (Exception $exception) {
    Console::log($exception->getMessage());
}
