<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class CommonTest extends TestCase
{
    private $pathToIndex;

    protected function setUp()
    {
        $this->pathToIndex = '../index.php';
    }

    /**
     * @dataProvider additionProvider
     * @param $expected
     * @param $options
     */
    public function testCliCommands($expected, $options)
    {
        exec('php ' . $this->pathToIndex . ' ' . implode(' ', $options), $output, $exitCode);
        $this->assertEquals($expected, $exitCode == 0);
    }

    public function additionProvider()
    {
        $correctInput = "files/correctInput.csv";
        $correctInput2 = "files/correctInput2.csv";
        $incorrectInput = 'files/incorrectInput.csv';

        $correctConfig = "files/correctConfig.php";
        $incorrectConfig = "files/incorrectConfig.php";

        $output = "files/output.csv";

        return [
            //недостаточно перданных параметров
            [false, []],
            [false, ['']],
            [false, ['gfgfgf']],
            [false, ['-help']],
            [false, ['--hlp']],
            [false, ['--strict']],
            [false, ['--skip-first']],
            [false, ['-i']],
            [false, ['-c']],
            [false, ['-o']],
            [false, ['--input']],
            [false, ['--config']],
            [false, ['--output']],
            [false, ['-i 111', '-c 111']],
            [false, ['-c 222', '-o 222']],
            [false, ['-i 333', '-o 333']],
            [false, ['-i 444', '-o 444', '-c 444']],
            [false, ["-i $correctInput", "-c $correctConfig", '--strict']],
            // некорректный разделитель
            [false, ["-i $correctInput", "-c $correctConfig", "-o $output", '-d"12"']],
            [false, ["-i $correctInput", "-c $correctConfig", "-o $output", '-d']],
            // некорректный тип конфига, инипут файла
            [false, ["-i $correctInput", "-c $correctInput", "-o $output"]],
            [false, ["-i $correctInput", "-c $output", "-o $output"]],
            [false, ["-i $correctConfig", "-c $correctConfig", "-o $output"]],
            [false, ["-i $correctConfig", "-c $correctInput", "-o $output"]],
            // некорректный конфиг, содержит больше столбцов чем в исходном файле
            [false, ["-i $correctInput", "-c $incorrectConfig", "-o $output", '--strict']],
            // неккоректный инпут файл, количество столбоц отличается
            [false, ["-i $incorrectInput", "-c $correctConfig", "-o $output"]],

            // правильный разделитель и файлы
            [true, ["-i $correctInput", "-c $correctConfig", "-o $output", '-d ","']],
            [true, ["-i $correctInput2", "-c $correctConfig", "-o $output", '-d ";"']],
            // пропускаем 1 строку
            [true, ["-i $correctInput", "-c $correctConfig", "-o $output", '--skip-first']],
            // стрикт с правильным конфигом
            [true, ["-i $correctInput", "-c $correctConfig", "-o $output", '--strict']],
            // вызов с корректными файлами и опциями
            [true, ["-i $correctInput", "-c $correctConfig", "-o $output"]],
            // вызов справки
            [true, ['-h']],
            [true, ['--help']],
        ];
    }

    public function testEqualOutputData()
    {
        $correctInput = 'files/correctInput.csv';
        $correctConfig = 'files/correctConfig.php';
        $output = 'files/output.csv';
        exec('php ' . $this->pathToIndex . ' -i '. $correctInput . ' -c ' . $correctConfig . ' -o ' . $output , $output, $exitCode);
        $this->assertEquals(file_get_contents('files/readyOutput.csv'), file_get_contents('files/output.csv'));
    }

    public function testEqualEncodingData()
    {
        $correctInput = 'files/win1251.csv';
        $correctConfig = 'files/correctConfig.php';
        $output = 'files/output.csv';
        exec('php ' . $this->pathToIndex . ' -i '. $correctInput . ' -c ' . $correctConfig . ' -o ' . $output , $output, $exitCode);
        $this->assertEquals(file_get_contents('files/outWin1251.csv'), file_get_contents('files/output.csv'));
    }

    public static function tearDownAfterClass()
    {
        if(file_exists('files/output.csv')) {
            unlink('files/output.csv');
        }
    }

}