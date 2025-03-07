<?php


namespace Tests;

use Exception;
use OlegV\Sheet;
use PHPUnit\Framework\TestCase;

class SheetTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAuth(): void
    {
        $sheet = new Sheet("1APvvpvUseIDJAvomQHeDvjHhbAjilSZvTR_P1UcwiDs",__DIR__.'/../config/sheet.json');
        $sheet->setSheet('Sheet9')->append([[1,2,"Text","Текст","many words many words many words",6,7,8,9]], 'R1C1');
        $this->expectNotToPerformAssertions();
    }
}