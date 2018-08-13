<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use services\CsvService;

class CsvServiceTest extends TestCase
{
    private $csvService;
    private $config;
    private $readedData;
    private $dataFromConfig;
    private $testOutputFile;

    protected function setUp()
    {
        $this->csvService = new CsvService();
        $this->config = require 'testFiles/testConf.php';
        $this->testOutputFile = 'testFiles/testOutput.csv';

        $this->readedData = [
            [
                0 => 'Номер',
                1 => 'Адрес',
                2 => 'ФИО',
                3 => 'Дом'
            ],
            [
                0 => '100',
                1 => 'Советский 2/7, кв. 23',
                2 => 'Иванов Иван Иванович',
                3 => '23'
            ],
            [
                0 => '101',
                1 => 'Советский 2/6, кв. 26',
                2 => 'Петров Иван Иванович',
                3 => '35'
            ]
        ];

        $this->dataFromConfig = [
            [
                0 => 'Номер',
                1 => '1',
                2 => '',
                3 => '2'
            ],
            [
                0 => '100',
                1 => '1',
                2 => '',
                3 => '6'
            ],
            [
                0 => '101',
                1 => '1',
                2 => '',
                3 => '0'
            ],
        ];
    }

    /**
     * Тест, правильно ли считвается файл
     */
    public function testReadCsvData()
    {
        $actualData = $this->csvService->readCsv('testFiles/testInput.csv', ',');
        $this->assertEquals($this->readedData, $actualData);
    }

    /**
     * Тест, правильно ли генерируется новый массив с данными исходя из файла конфига
     */
    public function testOutputDataFromConfig()
    {
        $actualData = $this->csvService->generateOutputDataFromConfig($this->readedData, $this->config, false);
        $this->assertEquals($this->dataFromConfig, $actualData);
    }

    /**
     * Тест, записывается ли в файл новые данные
     */
    public function testWriteCsv()
    {
        $this->csvService->writeCsv($this->dataFromConfig, $this->testOutputFile, ',', 'UTF-8');
        $this->assertTrue(file_exists($this->testOutputFile));

        $actualData = $this->csvService->readCsv($this->testOutputFile, ',');
        $this->assertEquals($this->dataFromConfig, $actualData);
    }

    /**
     * Тест, проверяет соответсвие входного файла, конфигурационному
     */
    public function testIsStrict()
    {
        $exceptTrue = $this->csvService->isStrict($this->readedData, $this->config);
        $this->assertTrue($exceptTrue);
    }

    /**
     * Удаляю созданный тестом testWriteCsv файл
     */
    public static function tearDownAfterClass()
    {
        if (file_exists('testFiles/testOutput.csv')) {
            unlink('testFiles/testOutput.csv');
        }
    }
}