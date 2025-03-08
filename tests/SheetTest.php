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
    public function testAppend(): void
    {
        $sheet = new Sheet("1cPmurA1B6TzUCvchc_wG3YnI0DwN0_9ZS5nsnZO8PRw", __DIR__.'/../credentials.json');
        $response = $sheet->setSheet('Sheet1')->append(
            [[1, 2, "Text", "Текст", "many words many words many words", 6, 7, 8, 9]],
            'R1C1',
        );
        $this->expectNotToPerformAssertions();

        //delete
        $data = json_decode($response, true);
        if (is_array($data) && isset($data['updates']) && is_array($data['updates'])) {
            if (isset($data['updates']['updatedRange']) && is_string($data['updates']['updatedRange'])) {
                $range = explode('!', $data['updates']['updatedRange']);
                $response = $sheet->clear($range[1]);
                print_r($response);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function testGetAndUpdate(): void
    {
        $sheet = new Sheet("1cPmurA1B6TzUCvchc_wG3YnI0DwN0_9ZS5nsnZO8PRw", __DIR__.'/../credentials.json');
        $sheet->setSheet('Sheet1');
        $response = $sheet->get(
            'A6:C6',
        );

        $response = $sheet->update(
            [["Text", "Текст", "many words many words many words"]],
            'A6:C6',
        );
        //try test workflow
        //return
        $sheet->update(
            [[1, 2, 3]],
            'A6:C6',
        );
        $this->expectNotToPerformAssertions();
    }
}