<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class LeadSpreadsheetParser
{
    public const MAX_DATA_ROWS = 5000;

    /** @return array{headers: array<int, string>, rows: array<int, array<int, string>>} */
    public function parse(string $path, string $extension): array
    {
        $extension = strtolower($extension);
        if ($extension === 'csv' || $extension === 'txt') {
            $records = $this->readCsv($path);
        } elseif (in_array($extension, ['xlsx', 'xls'], true)) {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $records = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $spreadsheet->disconnectWorksheets();
        } else {
            throw new RuntimeException('Choose a CSV, XLS, or XLSX file.');
        }

        $records = array_values(array_filter($records, fn ($row) => is_array($row) && collect($row)->contains(fn ($value) => trim((string) ($value ?? '')) !== '')));
        if (count($records) < 2) {
            throw new RuntimeException('This file needs a header row and at least one lead row.');
        }
        if (count($records) - 1 > self::MAX_DATA_ROWS) {
            throw new RuntimeException('For a smooth import, split this file into batches of '.number_format(self::MAX_DATA_ROWS).' leads or fewer.');
        }

        $headers = array_map(fn ($value) => trim((string) ($value ?? '')), array_shift($records));
        if (isset($headers[0])) $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        if (! collect($headers)->contains(fn (string $header) => $header !== '')) {
            throw new RuntimeException('The first row must contain column headings.');
        }

        $width = count($headers);
        $rows = array_map(function (array $row) use ($width): array {
            $row = array_slice(array_pad($row, $width, ''), 0, $width);
            return array_map(fn ($value) => trim((string) ($value ?? '')), $row);
        }, array_values($records));

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) throw new RuntimeException('The uploaded file could not be opened.');
        $rows = [];
        try {
            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                $rows[] = $row;
                if (count($rows) > self::MAX_DATA_ROWS + 1) break;
            }
        } finally {
            fclose($handle);
        }
        return $rows;
    }
}
