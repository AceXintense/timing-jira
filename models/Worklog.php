<?php

/*
 * Worklog for JIRA
 */

namespace Models;

class Worklog extends Model
{
    public $started;
    public $timeSpentSeconds;
    public $comment;
}