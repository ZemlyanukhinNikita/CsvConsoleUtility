<?php

require 'vendor/autoload.php';
require_once 'Services/CliParserService.php';
require_once 'domain/Options.php';
require_once 'Services/CsvService.php';

$opt = new CliParserService();
$options = $opt->parse();

if($options->getOptionHelp()) {
    $opt->printHelp();
    exit;
}

if (!$options->getOptionInput() || !$options->getOptionConfig() || !$options->getOptionOutput()) {
    echo 'Пример ввода: -i input.csv -c config.php -o output.csv (обязательные параметры)';
    $opt->printHelp();
    exit;
}


$flag = false;
if ($options->getOptionSkipFirst()) {
    $flag = true;
}

$csvService = new CsvService($options->getOptionInput(),$options->getOptionConfig(), $options->getOptionDelimiter(), $options->getOptionSkipFirst());

$readData = $csvService->readCsv();
$newData = $csvService->generateNewDataFromConfig($readData);
$csvService->writeCsv($newData);
//
//$inputData = [];
//if (($handle = fopen($options->getOptionInput(), "r")) !== FALSE) {
//    while (($data = fgetcsv($handle, 1000, $options->getOptionDelimiter())) !== FALSE) {
//        if ($flag) {
//            $flag = false;
//            continue;
//        }
//        $inputData[] = $data;
//    }
//    fclose($handle);
//}
//
//
//
//$faker = Faker\Factory::create();
//
//$config = require_once 'config.php';
//
//$newCsvData = [];
//foreach ($inputData as $index => $row) {
//    foreach ($row as $k => $value) {
//        if (!array_key_exists($k, $config)) {
//            $newCsvData[$index][$k] = $value;
//        } elseif(is_callable($config[$k])) {
//            $newCsvData[$index][$k] = $config[$k]($value, $row, $k, $faker);
//        } else {
//            $q = $config[$k];
//            $newCsvData[$index][$k] = !empty($q) ? $faker->$q : $q;
//        }
//    }
//}
//
//
//$fp = fopen($options->getOptionOutput(), 'w');
//
//foreach ($newCsvData as $fields) {
//    fputcsv($fp, $fields, $options->getOptionDelimiter());
//}
//
//fclose($fp);
