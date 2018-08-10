<?php

require_once __DIR__. DIRECTORY_SEPARATOR . '../services/CliParserService.php';
require_once __DIR__. DIRECTORY_SEPARATOR . '../domain/Options.php';

use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    private $pathToIndex;

    protected function setUp()
    {
        $this->pathToIndex = '../index.php';
    }

    /**
     * Тест на консольные команды
     * @dataProvider additionProvider
     * @param $excepted
     * @param $command
     */
    public function testCliOptions($excepted, $command)
    {
        exec('php ' . $this->pathToIndex . ' ' . $command, $output, $exitCode);
        $this->assertEquals($excepted, $exitCode == 0);
    }

    public function additionProvider()
    {
        $pathToInput = 'testFiles/testInput.csv';
        $pathToConfig = 'testFiles/testConf.php';
        $pathToOutput = 'testFiles/testOutput.csv';
        $pathToOutput2 = 'testFiles/testOutput2.csv';

        return [
            [true, '-h'],
            [true, '--help'],
            [true, '-h -h'],
            [true, '--help -h'],
            [true, '-i ' . $pathToInput . ' -c ' . $pathToConfig . ' -o ' . $pathToOutput],
            [true, '-i ' . $pathToInput . ' -c ' . $pathToConfig . ' -o ' . $pathToOutput2],
            [true, '-i ' . $pathToInput . ' -c ' . $pathToConfig . ' -o ' . $pathToOutput . ' --skip-first'],
            [true, '-i ' . $pathToInput . ' -c ' . $pathToConfig . ' -o ' . $pathToOutput . ' --strict'],
            [true, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput],
            [true, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput . ' --skip-first'],
            [true, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput . ' --skip-first --strict'],
            [true, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput . ' --strict --skip-first'],

            [false, ''],
            [false, 'gfgfgf'],
            [false, '-help'],
            [false, '--hlp'],
            [false, '--strict'],
            [false, '--skip-first'],
            [false, '--strict --skip-first'],
            [false, '-i ' . $pathToInput],
            [false, '--input ' . $pathToInput],
            [false, '-c testFiles/testConf.php'],
            [false, '--config testFiles/testConf.php'],
            [false, '-o testFiles/testOutput.csv'],
            [false, '--output testFiles/testOutput.csv'],
            [false, '--input ' . $pathToInput . ' --output ' . $pathToOutput],
            [false, '--input ' . $pathToInput . ' --config ' . $pathToConfig],
            [false, '--input ' . $pathToInput . ' --config testFiles/testConf.csv --output testFiles/testOutput.csv'],
            [false, '--input testFiles/testInput.php --config ' . $pathToConfig . ' --output ' . $pathToOutput],
            [false, '--input testFiles/testInput.php --config ' . $pathToConfig . ' --output testFiles/testOutput.php'],
            [false, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput . ' -strict'],
            [false, '--input ' . $pathToInput . ' --config ' . $pathToConfig . ' --output ' . $pathToOutput . ' -skip-first'],
        ];
    }

    /**
     * Удаляю созданные файлы тестом testTrueCliOptions
     */
    public static function tearDownAfterClass()
    {
        unlink('testFiles/testOutput.csv');
        unlink('testFiles/testOutput2.csv');
    }
}
