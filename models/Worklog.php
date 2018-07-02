<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/*
 * Worklog for JIRA
 */
class Worklog
{
    private $workLog;

    /**
     * Worklog constructor.
     * @param $issueKey
     * @param $remainingEstimateSeconds
     * @param $timeSpentSeconds
     * @param $dateStarted
     * @param string $comment
     */
    public function __construct($issueKey, $remainingEstimateSeconds, $timeSpentSeconds, $dateStarted, $comment = '')
    {
        $this->workLog = [
            'issue' => [
                'key' => $issueKey,
                'remainingEstimateSeconds' => $remainingEstimateSeconds,
            ],
            'timeSpentSeconds' => (int)$timeSpentSeconds,
            'dateStarted' => $dateStarted,
            'comment' => $comment,
            'author' => [
                'name' => Authentication::getUsername()
            ]
        ];
    }

    /**
     * Returns the array of values in a JSON string.
     * @return string
     */
    public function getWorklogJSON()
    {
        return json_encode($this->workLog);
    }

    /**
     * Returns the array of values assigned to the worklog.
     * @return array
     */
    public function getWorklog()
    {
        return $this->workLog;
    }

    /**
     * Calls the Tempo Restful API which we pass the CSV row data to.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadWorklogToJIRA()
    {
        $url = JIRA::getURL() . '/rest/tempo-timesheets/3/worklogs/';

        $headers = [
            'Authorization' => "Basic " . Authentication::getBasicAuthentication(),
            'Content-Type' => 'application/json; charset=utf-8'
        ];

        try {

            Console::log('Processing ' . $this->getWorklog()['issue']['key']);
            $request = new Request('POST', $url, $headers, $this->getWorklogJSON());
            $response = (new Client())->send($request);
            Console::log('Response HTTP : ' . $response->getStatusCode());

        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            Console::log('Cannot find Issue : ' . $this->getWorklog()['issue']['key']);
        }

    }
}