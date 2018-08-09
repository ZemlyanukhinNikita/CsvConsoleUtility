<?php

class CsvService
{
    /**
     * Метод, считывает данные с входого файла, и формирует массив из этих данных
     * @param $inputFile
     * @param $delim
     * @return array
     */
    public function readCsv($inputFile, $delim)
    {
        $inputData = [];
        $file = new SplFileObject($inputFile);
        while (!$file->eof()) {
            $line = $file->fgetcsv($delim);

            if ($file->key() == 0) {
                $countColumns = sizeof($line);
            }

            if (sizeof($line) !== $countColumns) {
                echo 'Неверный csv файл. Количество стобцов в файле не совпадает либо указан некорректный разделитель' . PHP_EOL;
                exit(1);
            }
            $inputData[] = $line;
        }
        return $inputData;
    }

    /**
     * Метод генерирует выходные данные по правилам описанным в файле конфига $configData
     * @param $inputData
     * @param $configData
     * @param $skipFirst
     * @return array
     */
    public function generateOutputDataFromConfig($inputData, $configData, $skipFirst)
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

                if (!array_key_exists($k, $configData)) {
                    $newCsvData[$index][$k] = $value;
                } elseif (is_callable($configData[$k])) {
                    $newCsvData[$index][$k] = $configData[$k]($value, $row, $index, $faker);
                } else {
                    $property = $configData[$k];
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

    /**
     * Метод записывает данные $csvData в выходной файл $outputFile
     * @param $csvData
     * @param $outputFile
     * @param $delim
     * @param $encoding
     */
    public function writeCsv($csvData, $outputFile, $delim, $encoding)
    {
        $fp = fopen($outputFile, 'w');

        foreach ($csvData as $fields) {
            foreach ($fields as $key => $field) {
                $encodeLine = mb_detect_encoding($field, ['UTF-8', 'Windows-1251']);

                if ($encodeLine != $encoding) {
                    $field = mb_convert_encoding($field, $encoding, $encodeLine);
                }
                $fields[$key] = $field;
            }
            fputcsv($fp, $fields, $delim);
        }

        $stat = fstat($fp);
        ftruncate($fp, $stat['size'] - 1);
        fclose($fp);
    }

    /**
     * Метод проверяет соответсвует ли файл конфига $configFile входному файлу
     * в файле конфига должно быть меньшее или одинаковое количество столбцов
     * относительно исходого файла
     * @param $inputData
     * @param $configFile
     * @return bool
     */
    public function isStrict($inputData, $configFile): bool
    {
        return (count($inputData[0]) <= max(array_keys($configFile))) ? false : true;
    }
}