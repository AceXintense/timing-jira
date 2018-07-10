<?php

/*
 * Worklog for JIRA
 */

namespace Models;

class Worklog extends Model
{
    public $issueKey;
    public $started;
    public $timeSpentSeconds;
    public $comment;
}