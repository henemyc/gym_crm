<?php

namespace Tests\Unit;

use App\Services\LeadSpreadsheetParser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class LeadSpreadsheetParserTest extends TestCase
{
    public function test_csv_headers_and_rows_are_parsed_without_blank_lines(): void
    {
        $directory = sys_get_temp_dir().'/gymxbook-tests';
        if (! is_dir($directory)) mkdir($directory, 0775, true);
        $path = $directory.'/leads.csv';
        file_put_contents($path, "Name,Gym Name,Phone\nAnanya Sharma,PulseFit Studio,9876543210\n,,\n");

        try {
            $parsed = (new LeadSpreadsheetParser())->parse($path, 'csv');
            self::assertSame(['Name', 'Gym Name', 'Phone'], $parsed['headers']);
            self::assertSame([['Ananya Sharma', 'PulseFit Studio', '9876543210']], $parsed['rows']);
        } finally {
            @unlink($path);
            @rmdir($directory);
        }
    }

    public function test_xlsx_headers_and_rows_are_parsed(): void
    {
        $directory = sys_get_temp_dir().'/gymxbook-tests';
        if (! is_dir($directory)) mkdir($directory, 0775, true);
        $path = $directory.'/leads.xlsx';
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray([
            ['Name', 'Gym Name', 'Phone'],
            ['Ananya Sharma', 'PulseFit Studio', '9876543210'],
        ], null, 'A1');
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $parsed = (new LeadSpreadsheetParser())->parse($path, 'xlsx');
            self::assertSame(['Name', 'Gym Name', 'Phone'], $parsed['headers']);
            self::assertSame([['Ananya Sharma', 'PulseFit Studio', '9876543210']], $parsed['rows']);
        } finally {
            @unlink($path);
            @rmdir($directory);
        }
    }
}
