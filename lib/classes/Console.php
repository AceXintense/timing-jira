<?php

/*
 * Simple class to handle the console logging.
 */
class Console
{

    /**
     * Logs simple message to the CLI.
     * @param $message
     */
    public static function log($message)
    {
        print_r($message . PHP_EOL);
    }

}