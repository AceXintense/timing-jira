<?php

/*
 * Simple JIRA class that stores the URL this can have functionality added later.
 */

namespace Core;

use DateTime;
use Exceptions\DataTypeException;
use Exceptions\JIRAException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Models\Worklog;

/*
 * Simple JIRA interaction for the script to access what it needs to.
 */
class JIRA
{
    private static $URL;
    const APILocations = [
        'getIssueWorklog' => [
            'type' => 'GET',
            'url' => '/rest/api/2/issue/{issueKey}/worklog',
            'parameters' => [
                'issueKey' => DataType::STRING
            ]
        ],
        'addWorklog' => [
            'type' => 'POST',
            'url' => '/rest/api/2/issue/{issueKey}/worklog',
            'parameters' => [
                'issueKey' => DataType::STRING
            ],
            'requestStructure' => [
                'comment' => [
                    'type' => DataType::STRING,
                    'required' => false
                ],
                'started' => [
                    'type' => DataType::STRING,
                    'required' => false
                ],
                'timeSpentSeconds' => [
                    'type' => DataType::FLOAT,
                    'required' => true
                ],
            ]
        ],
        'getWorklog' => [
            'type' => 'GET',
            'url' => '/rest/api/2/issue/{issueKey}/worklog/{id}',
            'parameters' => [
                'issueKey' => DataType::STRING,
                'id' => DataType::INTEGER
            ]
        ],
        'updateWorklog' => [
            'type' => 'PUT',
            'url' => '/rest/api/2/issue/{issueKey}/worklog/{id}',
            'parameters' => [
                'issueKey' => DataType::STRING,
                'id' => DataType::INTEGER
            ]
        ],
        'deleteWorklog' => [
            'type' => 'DELETE',
            'url' => '/rest/api/2/issue/{issueKey}/worklog/{id}',
            'parameters' => [
                'issueKey' => DataType::STRING,
                'id' => DataType::INTEGER
            ]
        ]
    ];

    /**
     * Adds a worklog to the JIRA server.
     * @param $issueKey
     * @param $data
     * @throws DataTypeException
     * @throws JIRAException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addWorklog($issueKey, $data)
    {
        if (!self::checkForPreviousWorklog($issueKey, $data)) {
            Console::log("$issueKey Processing");
            self::callAPI('addWorklog', $data, $issueKey);
            Console::log("$issueKey Success");
            return;
        }
        Console::log("$issueKey already has a Worklog at this time skipping...");
    }

    /**
     * Returns a worklog from the api using the issue key.
     * @param $issueKey
     * @param $data
     * @throws DataTypeException
     * @throws JIRAException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getIssueWorklog($issueKey)
    {
        self::callAPI('getIssueWorklog', null, $issueKey);
    }

    /**
     * Returns headers array which will be used in the calling of the API.
     * @return array
     */
    private static function getHeaders()
    {
        return [
            'Authorization' => 'Basic ' . Authentication::getBasicAuthentication(),
            'Content-Type' => 'application/json; charset=utf-8'
        ];
    }


    /**
     * Checks to see if there is a worklog already in the new worklog start position.
     * @param $issueKey
     * @param $data
     * @return bool
     * @throws DataTypeException
     * @throws JIRAException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function checkForPreviousWorklog($issueKey, $data)
    {
        $worklogs = self::callAPI('getIssueWorklog', $data, $issueKey)->worklogs;
        foreach ($worklogs as $worklog) {
            if ($data->started === $worklog->started) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a request type such as POST, GET, PUT, DELETE
     * @param $APILocation
     * @return string
     */
    private static function getRequestType($APILocation)
    {
        return self::APILocations[$APILocation]['type'];
    }

    /**
     * Checks the request before sending the request.
     * @param $APILocation
     * @param $data
     * @throws DataTypeException
     * @throws JIRAException
     */
    private static function checkRequest($APILocation, $data)
    {
        if ($APILocation['type'] === 'POST') {
            //Loops over the keys and checks to see if it matches the data structure.
            foreach ($data as $key => $value) {
                //Checks to see if key exists in structure.
                if (!empty($APILocation['requestStructure'][$key])) {
                    //Checks to see if the value of the key is the correct dataType.
                    if (!DataType::isDataType($APILocation['requestStructure'][$key]['type'], $value)) {
                        throw new DataTypeException($value . ' is not ' . $APILocation['requestStructure'][$key]['type']);
                    }
                    continue;
                }
                if (!empty($APILocation['parameters'][$key])) {
                    unset($data->$key);
                    continue;
                }
                throw new JIRAException($APILocation['url']. $key . ' is not in the data structure');
            }
        }
    }

    /**
     * Calls the API using the URL and the location followed by the data and the URL parameters.
     * @param $location
     * @param Worklog $data
     * @param mixed ...$parameters
     * @return string
     * @throws DataTypeException
     * @throws JIRAException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function callAPI($location, $data = null, ...$parameters)
    {
        self::checkRequest(self::APILocations[$location], $data);
        try {
            $request = new Request(
                self::getRequestType($location),
                self::generateURL($location, $parameters),
                self::getHeaders(),
                json_encode($data)
            );
            $response = (new Client())->send($request);
            return json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            if ($exception->getCode() === 403) {
                die('Access is denied.');
            }
            Console::log('Cannot find Issue : ' . $data->issueKey);
        }
    }

    /**
     * Generates the URL using the base url and applying the parameters to the URL.
     * @param $location
     * @param $parameters
     * @return string
     */
    public static function generateURL($location, $parameters)
    {
        $url = self::getURL() . self::APILocations[$location]['url'];
        foreach ($parameters as $parameter) {
            $url = preg_replace('/\{\w+\}/', $parameter, $url);
        }
        return $url;
    }

    /**
     * Sets the hostname for the JIRA instance.
     * @param string $uri
     */
    public static function setURL($uri)
    {
        self::$URL = $uri;
    }

    /**
     * Returns the hostname for the JIRA instance.
     * @return string
     */
    public static function getURL()
    {
        return self::$URL;
    }

    /**
     * Formats a date to a 'JIRA' compatible date....
     * @param $date
     * @return string
     */
    public static function formatDate($date)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format("Y-m-d\TH:i:s").'.000'.$dateTime->format('O');
    }

}