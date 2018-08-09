<?php

require 'vendor/autoload.php';
require_once 'services/CliParserService.php';
require_once 'domain/Options.php';
require_once 'services/CsvService.php';

$opt = new CliParserService();
$options = $opt->parse();

$pathToInputFile = $options->getOptionInput();
$pathToConfigFile = $options->getOptionConfig();
$pathToOutputFile = $options->getOptionOutput();
$delimiter = $options->getOptionDelimiter();
$skipFirst = $options->getOptionSkipFirst();
$strict = $options->getOptionStrict();
$help = $options->getOptionHelp();

if ($help) {
    $opt->printHelp();
    exit(0);
}

if (!$pathToInputFile || !$pathToConfigFile || !$pathToOutputFile) {
    echo 'Пример ввода: -i input.csv -c config.php -o output.csv (обязательные параметры)';
    $opt->printHelp();
    exit(1);
}

$encoding = mb_detect_encoding(file_get_contents($pathToInputFile),['UTF-8', 'Windows-1251']);
//$encoding = mb_check_encoding(file_get_contents($pathToInputFile),'UTF-8') ? 'UTF-8' : 'Windows-1251';
if ($encoding !== 'UTF-8' && $encoding !== 'Windows-1251') {
    echo 'Неверная кодировка исходного файла';
    exit(1);
}

iconv_set_encoding('output_encoding', $encoding);

$config = require_once $pathToConfigFile;

$csvService = new CsvService();

$inputData = $csvService->readCsv($pathToInputFile,$delimiter);

if ($strict && !$csvService->isStrict($inputData, $config)) {
    echo 'Конфигурационный файл имеет больше столбцов чем исходный';
    exit(1);
}

$newCsvData = $csvService->generateOutputDataFromConfig($inputData, $config, $skipFirst);
$csvService->writeCsv($newCsvData, $pathToOutputFile, $delimiter, $encoding);
echo 'Файл '. $pathToOutputFile . ' успешно создан или обновлен' . PHP_EOL;
exit(0);