<?php

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Models\Worklog;
use Models\Authentication;
use Models\JIRA;

include "vendor/autoload.php";

include "exceptions/DataTypeException.php";
include "exceptions/JIRAException.php";

include "./Console.php";

// Reads the CSV and creates an array of rows from the CSV data.
$rows = [];
$file = fopen('./data/Timing Export.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
    $rows[] = $line;
}
fclose($file);

// Formats the array to have the header row as a key per value e.g [ 'Project' => 'Test Project' ] instead of [ 0 => 'Test Project' ]
$formattedRows = [];
foreach ($rows as $index => $row) {
    if ($index !== 0) {
        foreach ($row as $key => $value) {
            $formattedRows[$index][$rows[0][$key]] = $value;
        }
    }
}


//Sets the Authentication for JIRA.
Authentication::setAuthenticationFromFile('credentials.json');
//Sets the URL for the JIRA instance.
JIRA::setURL('');

try {
    //Loop through each row on the CSV and upload the Data.
    foreach ($formattedRows as $formattedRow) {
        JIRA::addWorklog('WEB-530', new Worklog(JIRA::formatDate($formattedRow['Start Date']), (float)$formattedRow['Duration'], $formattedRow['Notes']));
    }
} catch (Exception $exception) {
    Console::log($exception->getMessage());
}
