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
}