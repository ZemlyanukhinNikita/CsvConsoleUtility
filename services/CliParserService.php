<?php

namespace services;

use domain\Options;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;
use GetOpt\GetOpt;
use GetOpt\Option;

class CliParserService
{
    private $getopt;

    public function __construct()
    {
        $this->getopt = new GetOpt();

        $this->getopt->addOptions([
            Option::create('i', 'input', GetOpt::REQUIRED_ARGUMENT)
                ->setArgumentName('input file')
                ->setDescription('Путь до исходного файла')
                ->setValidation(function ($filename) {
                    if (!is_readable($filename)) {
                        echo 'Исходный файл не существует, либо недоступен для чтения' . PHP_EOL;
                        return false;
                    }

                    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'csv' &&
                        mime_content_type($filename) !== 'text/plain') {
                        echo 'Исходный файл должен быть с расширением .csv' . PHP_EOL;
                        return false;
                    }
                    return true;
                }),
            Option::create('c', 'config', GetOpt::REQUIRED_ARGUMENT)
                ->setArgumentName('config file')
                ->setDescription('Путь до файла конфигурации')
                ->setValidation(function ($filename) {
                    if (!is_readable($filename)) {
                        echo 'Конфигурационный файл не существует, либо недоступен для чтения' . PHP_EOL;
                        return false;
                    }

                    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'php' &&
                        mime_content_type($filename) !== 'text/x-php') {
                        echo 'Исходный файл должен быть с расширением .csv' . PHP_EOL;
                        return false;
                    }
                    return true;
                }),
            Option::create('o', 'output', GetOpt::REQUIRED_ARGUMENT)
                ->setArgumentName('output file')
                ->setDescription('Путь до файла с результатом')
                ->setValidation(function ($filename) {
                    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'csv') {
                        echo 'Выходной файл должен быть с расширением .csv' . PHP_EOL;
                        return false;
                    }

                    if (!is_writable($filename) && is_file($filename)) {
                        echo 'Файл не существует, либо недоступен для записи' . PHP_EOL;
                        return false;
                    }
                    return true;
                }),
            Option::create('d', 'delimiter', GetOpt::REQUIRED_ARGUMENT)
                ->setDescription("Задать разделитель (по умолчанию “,”) для символа табуляции используйте ввод $'\\t'")
                ->setDefaultValue(',')
                ->setValidation(function ($delimiter) {
                    if (!preg_match('(^\W$|\t)', $delimiter)) {
                        return false;
                    }
                    return true;
                }),
            Option::create(null, 'skip-first', GetOpt::NO_ARGUMENT)
                ->setDefaultValue(false)
                ->setDescription('Пропускать модификацию первой строки исходного csv'),
            Option::create(null, 'strict', GetOpt::NO_ARGUMENT)
                ->setDefaultValue(false)
                ->setDescription('Проверяет, что исходный файл содержит необходимое количество описанных в конфигурационном файле столбцов'),
            Option::create('h', 'help', GetOpt::NO_ARGUMENT)
                ->setDescription('Справка, показывает это сообщение'),
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
            } catch (Missing $e) {
                if (!$this->getopt->getOption('help')) {
                    throw $e;
                }
            }
        } catch (ArgumentException $e) {
            echo $e->getMessage();
            echo PHP_EOL . $this->getopt->getHelpText();
            exit(1);
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
