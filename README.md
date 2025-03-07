# sheets

## Usage
```php
//append
$sheet = new Sheet("sheets_id",__DIR__.'/../config/sheet.json');
$sheet->setSheet('Sheet9')->append([[1,2,"Text","Текст","many words many words many words",6,7,8,9]], 'R1C1');
```