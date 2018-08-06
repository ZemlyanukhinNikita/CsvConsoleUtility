<?php

class CsvService
{
    private $pathToFile;
    private $delim;
    private $skipFirst;
    private $pathToConfig;
    private $pathToOutputFile;

    /**
     * CsvService constructor.
     * @param $pathToFile
     * @param $pathToConfig
     * @param $delim
     * @param $skipFirst
     * @param $pathToOutputFile
     */
    public function __construct($pathToFile, $pathToConfig, $delim, $skipFirst, $pathToOutputFile)
    {
        $this->pathToFile = $pathToFile;
        $this->delim = $delim;
        $this->skipFirst = $skipFirst;
        $this->pathToConfig = $pathToConfig;
        $this->pathToOutputFile = $pathToOutputFile;
    }

    public function readCsv()
    {
        $inputData = [];
        if (($handle = fopen($this->pathToFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $this->delim)) !== FALSE) {
                if ($this->skipFirst) {
                    $this->skipFirst = false;
                    continue;
                }
                $inputData[] = $data;
            }
            fclose($handle);
        }
        return $inputData;
    }

    public function generateNewDataFromConfig($inputData)
    {
        $faker = Faker\Factory::create();
        $config = require_once $this->pathToConfig;

        $newCsvData = [];
        foreach ($inputData as $index => $row) {
            foreach ($row as $k => $value) {
                if (!array_key_exists($k, $config)) {
                    $newCsvData[$index][$k] = $value;
                } elseif (is_callable($config[$k])) {
                    $newCsvData[$index][$k] = $config[$k]($value, $row, $k, $faker);
                } else {
                    $q = $config[$k];
                    $newCsvData[$index][$k] = !empty($q) ? $faker->$q : $q;
                }
            }
        }
        return $newCsvData;
    }

    public function writeCsv($csvData)
    {
        $fp = fopen($this->pathToOutputFile, 'w');

        foreach ($csvData as $fields) {
            fputcsv($fp, $fields, $this->delim);
        }

        fclose($fp);
    }


}