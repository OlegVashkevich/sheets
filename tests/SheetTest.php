<?php


namespace Tests;

use Error;
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
        $sheet->get(
            'A6:C6',
        );

        $sheet->update(
            [["Text", "Текст", "many words many words many words"]],
            'A6:C6',
        );

        //return
        $sheet->update(
            [[1, 2, 3]],
            'A6:C6',
        );
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Exception
     */
    public function testNotCredentialsFile(): void
    {
        $this->expectExceptionMessage('The file with credentials file does not exist.');
        $sheet = new Sheet("1cPmurA1B6TzUCvchc_wG3YnI0DwN0_9ZS5nsnZO8PRw", __DIR__.'/../bad_credentials.json');
    }

    /**
     * @throws Exception
     */
    public function testNotFullCredentialsFile(): void
    {
        $this->expectException(Error::class);
        $sheet = new Sheet("1cPmurA1B6TzUCvchc_wG3YnI0DwN0_9ZS5nsnZO8PRw", __DIR__.'/data/not_full.json');
    }

    /**
     * @throws Exception
     */
    public function testEmptyPKCredentialsFile(): void
    {
        $this->expectExceptionMessage('Could not resolve host: token_uri');
        $sheet = new Sheet("1cPmurA1B6TzUCvchc_wG3YnI0DwN0_9ZS5nsnZO8PRw", __DIR__.'/data/empty_private_key.json');
    }
}