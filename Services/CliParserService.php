<?php

use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;
use GetOpt\GetOpt;
use GetOpt\Option;

class CliParserService
{
    private $getopt;

    public function __construct()
    {
        $this->getopt = new GetOpt([
            Option::create('i', 'input', GetOpt::REQUIRED_ARGUMENT)
                ->setArgumentName('input file')
                ->setDescription('Путь до исходного файла')
                ->setValidation(function ($filename) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    return $ext === 'csv' ? true : false;
                })
                ->setValidation('is_readable'),
            Option::create('c', 'config', GetOpt::REQUIRED_ARGUMENT)->setArgumentName('config file')
                ->setDescription('Путь до файла конфигурации')
                ->setValidation(function ($filename) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    return $ext === 'php' ? true : false;
                })->setValidation('is_readable'),
            Option::create('o', 'output', GetOpt::REQUIRED_ARGUMENT)->setArgumentName('output file')
                ->setDescription('Путь до файла с результатом')
                ->setValidation(function ($filename) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    return $ext === 'csv' && (is_writable($filename) || !is_file($filename)) ? true : false;
                }),
            Option::create('d', 'delimiter', GetOpt::REQUIRED_ARGUMENT)
                ->setDescription('Задать разделитель (по умолчанию “,”)')
                ->setDefaultValue(','),
            Option::create(null, 'skip-first', GetOpt::NO_ARGUMENT)->setDefaultValue(false)->setDescription('Пропускать модификацию первой строки исходного csv'),
            Option::create(null, 'strict', GetOpt::NO_ARGUMENT)->setDefaultValue(false)->setDescription('Проверяет, что исходный файл содержит необходимое количество описанных в конфигурационном файле столбцов'),
            Option::create('h', 'help', GetOpt::NO_ARGUMENT)->setDescription('Справка, показывает это сообщение'),
        ]);

    }

    /**
     * @return Options
     */
    public function parse()
    {
        try {
            try {
                $this->getopt->process();
                $options = new Options();
                $options->setOptionInput($this->getopt->getOption('input'));
                $options->setOptionConfig($this->getopt->getOption('config'));
                $options->setOptionOutput($this->getopt->getOption('output'));
                $options->setOptionDelimiter($this->getopt->getOption('delimiter'));
                $options->setOptionSkipFirst($this->getopt->getOption('skip-first'));
                $options->setOptionStrict($this->getopt->getOption('strict'));
                $options->setOptionHelp($this->getopt->getOption('help'));
                return $options;

            } catch (Missing $exception) {
                // catch missing exceptions if help is requested
                if (!$this->getopt->getOption('help')) {
                    throw $exception;
                }
            }
        } catch (ArgumentException $exception) {
            echo $exception->getMessage();
            echo PHP_EOL . $this->getopt->getHelpText();
        }
    }

    /**
     * Печатает справку
     */
    public function printHelp()
    {
        echo PHP_EOL . $this->getopt->getHelpText();
    }
}
