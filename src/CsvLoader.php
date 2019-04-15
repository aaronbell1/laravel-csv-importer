<?php

namespace Aaronbell1\LaravelCsvImporter;

use Aaronbell1\LaravelCsvImporter\Exceptions\HeaderRowUnmatchedException;

ini_set('auto_detect_line_endings', true);

class CsvLoader
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $rows;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    private $temp;

    /**
     * Get CSV from file path and load into array data
     *
     * @param $filePath
     * @throws HeaderRowUnmatchedException
     */
    public function load($filePath)
    {
        $this->filePath = $filePath;

        $this->storeCsvTemp();

        $this->extractRows();
        $this->combineKeys();
    }

    private function storeCsvTemp()
    {
        $fp = fopen($this->filePath, 'r');

        while ($row = fgetcsv($fp)) {
            $this->temp[] = $row;
        }

        fclose($fp);
    }

    /**
     * Extract header and rows to separate variables
     */
    private function extractRows()
    {
        $this->setHeader($this->temp);
        $this->setRows($this->temp);
    }

    /**
     * From the loaded CSV, extract the first row as header
     *
     * @param $data
     */
    private function setHeader($data)
    {
        $this->headers = array_map('self::formatString', $data[0]);
        array_unshift($this->headers, 'row');
    }

    /**
     * From the loaded CSV, extract empty rows after header and
     * store them in an array
     *
     * @param $data
     */
    private function setRows($data)
    {
        $this->rows = array_slice(array_filter($data, function ($a) {
            return array_filter($a);
        }), 1, null, true);
    }

    /**
     * Combine the header array as keys for each row
     *
     * @throws HeaderRowUnmatchedException
     */
    private function combineKeys()
    {
        if ($this->rows) {
            try {
                foreach ($this->rows as $key => $row) {
                    array_unshift($row, $key + 1);
                    $this->data[] = array_combine($this->headers, $row);
                }
            } catch (\ErrorException $e) {
                throw new HeaderRowUnmatchedException();
            }
        }
    }

    /**
     * Return rows from the loaded data at the given index
     *
     * @param array $indexes
     * @return array
     */
    public function getRowsByIndexes(array $indexes)
    {
        return array_filter($this->data, function ($key) use ($indexes) {
            return in_array($key, $indexes);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Delete a row from the data at the given index
     *
     * @param $index
     */
    public function deleteRow($index)
    {
        unset($this->data[$index]);
    }

    /**
     * Return the number of rows loaded
     *
     * @return int
     */
    public function countRows()
    {
        return count($this->rows);
    }

    /**
     * Return the number of total rows remaining
     *
     * @return int
     */
    public function countData()
    {
        return count($this->data);
    }

    static function formatString($string)
    {
        return str_replace(' ', '_', mb_strtolower($string));
    }
}
