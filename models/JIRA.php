<?php

/*
 * Simple JIRA class that stores the URL this can have functionality added later.
 */
class JIRA
{
    private static $URL;

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

}