# Sheets
Lib for work with google sheets
## Features
- lightweight
- dependency-free(only curl and openssl)
- 90+% test coverage
- phpstan max lvl
- phpstan full strict rules

## Install
```shell
composer requier olegv/sheets
```

## Usage
1. You must create project and add to project `service account` [here](https://console.cloud.google.com/projectselector2/iam-admin/serviceaccounts?hl=ru&inv=1&invt=AbreJg&supportedpurview=project)
2. In `service account` must create `key` in kes tab, after that your credentials will be downloaded as a json file
3. Put json file with credentials somewhere, for example `/path_to/credentials.json`
4. Ydoou may need to add the email address from your credentials to sheet access
5. Code example 
```php
use OlegV\Sheet;

$sheet = new Sheet("sheets_id",'/path_to/credentials.json');


//append
$response = ->setSheet('Sheet9')->append([[1,2,"Text","Текст","many words many words many words",6,7,8,9]], 'R1C1');
print_r($response);


//clear
$data = json_decode($response, true);
$range = explode('!', $data['updates']['updatedRange']);
$response = $sheet->clear($range[1]);
print_r($response);


//get
$response = $sheet->get(
    'A6:C6',
);
print_r($response);

//update
$response = $sheet->update(
    [[1, 2, 3]],
    'A6:C6',
);
print_r($response);
```
