<?php

use Core\Authentication;
use Core\Console;
use Core\CSVParser;
use Core\JIRA;
use Models\Worklog;
include 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
/*
 * Initial script startup setting JIRA Login, URL, CSV Data.
 */
CSVParser::getInstance()->load('./data/Timing Export.csv');
Authentication::setUsernameAndPassword(getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD'), getenv('JIRA_PASSWORD_BASE64'));
JIRA::setURL(getenv('JIRA_URL'));

try {
    //Loop through each row on the CSV and upload the Data.
    foreach (CSVParser::getInstance()->format()->getFormattedRows() as $formattedRow) {
        JIRA::addWorklog(
            $formattedRow['Task Title'],
            new Worklog(
                $formattedRow['Task Title'],
                JIRA::formatDate($formattedRow['Start Date']),
                (float)$formattedRow['Duration'],
                $formattedRow['Notes']
            )
        );
    }
} catch (Exception $exception) {
    Console::log($exception->getMessage());
} catch (\GuzzleHttp\Exception\GuzzleException $exception) {
    Console::log($exception->getMessage());
}
