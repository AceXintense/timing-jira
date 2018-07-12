<?php

namespace Models;

/*
 * Model which has the data structure of the JIRA Worklog.
 */
class Worklog extends Model
{
    public $issueKey;
    public $started;
    public $timeSpentSeconds;
    public $comment;

    const DASH_SEPARATED_ISSUE_NUMBER = '/\w+\-\d+/';
    const UNDERSCORE_SEPARATED_ISSUE_NUMBER = '/\w+\_\d+/';

    public static function getIssueKey($issueTitle, $matchRegex)
    {
        preg_match($matchRegex, $issueTitle,$matches);
        return $matches[0];
    }
}