<?php

class CsvService
{

    public function convertToNewCsv($inputFile, $config, $outputFile, $delim, $skipFirst, $strict)
    {
        $readCsvData = $this->readCsv($inputFile, $delim);

        if ($strict && !$this->isStrict($readCsvData, $config)) {
            echo 'Конфигурационный файл имеет больше столбцов чем исходный';
            exit();
        }

        $newCsvData = $this->generateNewDataFromConfig($readCsvData, $config, $skipFirst);
        $this->writeCsv($newCsvData, $outputFile, $delim);
    }

    private function isStrict($inputFile, $configFile): bool
    {
        return (count($inputFile[0]) < max(array_keys($configFile)) + 1) ? false : true;
    }

    private function readCsv($inputFile, $delim)
    {
        $inputData = [];
        if (($handle = fopen($inputFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delim)) !== FALSE) {
                $inputData[] = $data;
            }
            fclose($handle);
        }
        return $inputData;
    }

    private function generateNewDataFromConfig($inputData, $config, $skipFirst)
    {
        $newCsvData = [];
        $faker = Faker\Factory::create();

        foreach ($inputData as $index => $row) {

            if ($skipFirst) {
                $skipFirst = false;
                $newCsvData[$index] = $row;
                continue;
            }

            foreach ($row as $k => $value) {

                if (!array_key_exists($k, $config)) {
                    $newCsvData[$index][$k] = $value;
                } elseif (is_callable($config[$k])) {
                    $newCsvData[$index][$k] = $config[$k]($value, $row, $k, $faker);
                } else {
                    $property = $config[$k];
                    //Проверяем можно ли вызвать свойство с файла конфига у фейкера, если такого нет, запишем то что в конфиге как строку
                    try {
                        $fakerProperty = $faker->$property;
                        $newCsvData[$index][$k] = $fakerProperty;
                    } catch (Exception $e) {
                        $newCsvData[$index][$k] = $property;
                    }
                }
            }
        }
        return $newCsvData;
    }

    private function writeCsv($csvData, $outputFile, $delim)
    {
        $fp = fopen($outputFile, 'w');

        foreach ($csvData as $fields) {
            fputcsv($fp, $fields, $delim);
        }
        fclose($fp);
    }
}