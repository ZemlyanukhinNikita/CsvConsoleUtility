<?php

require 'vendor/autoload.php';
require_once 'Services/CliParserService.php';
require_once 'domain/Options.php';
require_once 'Services/CsvService.php';

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
    exit;
}

if (!$pathToInputFile || !$pathToConfigFile || !$pathToOutputFile) {
    echo 'Пример ввода: -i input.csv -c config.php -o output.csv (обязательные параметры)';
    $opt->printHelp();
    exit;
}

$encoding = mb_detect_encoding(file_get_contents($pathToInputFile));

if ($encoding !== 'UTF-8' && $encoding !== 'CP1251') {
    echo 'Неверная кодировка исходного файла';
    exit();
}

$config = require_once $pathToConfigFile;

$csvService = new CsvService();

$csvService->convertToNewCsv(
    $pathToInputFile,
    $config,
    $pathToOutputFile,
    $delimiter,
    $skipFirst,
    $strict
);
