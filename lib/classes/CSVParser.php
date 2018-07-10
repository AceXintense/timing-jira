<?php

namespace Core;

class CSVParser
{
    private static $instance;
    private $file;
    private $fileContents;
    private $csvRows = [];
    private $formattedRows = [];

    /**
     * Reads a .csv file and stores the data in the class as an array of rows of data.
     * @return CSVParser
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function load($filePath)
    {
        $this->file = fopen($filePath, 'r');
        $this->fileContents = file_get_contents($filePath);
        while (($line = fgetcsv($this->file)) !== FALSE) {
            $this->csvRows[] = $line;
        }
        fclose($this->file);

        return $this;
    }

    /**
     * Returns the RAW file contents for the CSV.
     * @returns string
     */
    public function getContents()
    {
        return $this->fileContents;
    }

    /**
     * Get rows from the CSV file.
     * @returns array
     */
    public function getRows()
    {
        return $this->csvRows;
    }

    /**
     * Get formatted rows from the CSV file.
     * @returns array
     */
    public function getFormattedRows()
    {
        return $this->formattedRows;
    }

    /**
     * Formats the array to have the key as the corresponding value.
     * @returns array
     */
    public function format()
    {
        // Formats the array to have the header row as a key per value e.g [ 'Project' => 'Test Project' ] instead of [ 0 => 'Test Project' ]
        foreach ($this->csvRows as $index => $row) {
            if ($index !== 0) {
                foreach ($row as $key => $value) {
                    $this->formattedRows[$index][$this->csvRows[0][$key]] = $value;
                }
            }
        }
        return $this;
    }

}