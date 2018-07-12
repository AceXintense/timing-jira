<?php

namespace Core;

/*
 * Handles files in a simple to use wrapper.
 */
class Files
{
    private static $instance;

    /**
     * Reads a .csv file and stores the data in the class as an array of rows of data.
     * @return Files
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $filePath
     * @return array
     */
    public function getFilesInDirectory($filePath)
    {
        return array_diff(scandir($filePath, SCANDIR_SORT_NONE), ['.', '..']);
    }

}