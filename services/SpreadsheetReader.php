<?php

namespace app\services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetReader
{
    private const CHUNK_SIZE = 1000;

    private const COLUMN_MAP = [
        'A' => 'company',
        'B' => 'region',
        'C' => 'city',
        'D' => 'invoice_date',
        'E' => 'delivery_address',
        'F' => 'legal_address',
        'G' => 'client',
        'H' => 'client_code',
        'I' => 'client_department_code',
        'J' => 'client_okpo',
        'K' => 'license',
        'L' => 'license_expiry_date',
        'M' => 'product_code',
        'N' => 'barcode',
        'O' => 'product',
        'P' => 'morion_code',
        'Q' => 'unit',
        'R' => 'manufacturer',
        'S' => 'supplier',
        'T' => 'quantity',
        'U' => 'warehouse',
    ];

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getTotalRows(): int
    {
        $reader = IOFactory::createReaderForFile($this->filePath);
        $info = $reader->listWorksheetInfo($this->filePath);

        return $info[0]['totalRows'];
    }

    /**
     * Yields rows in chunks to avoid memory overflow.
     * Skips the header row (row 1).
     *
     * @return \Generator<array>
     */
    public function readRows(): \Generator
    {
        $totalRows = $this->getTotalRows();
        $reader = IOFactory::createReaderForFile($this->filePath);
        $reader->setReadDataOnly(true);

        for ($startRow = 2; $startRow <= $totalRows; $startRow += self::CHUNK_SIZE) {
            $endRow = min($startRow + self::CHUNK_SIZE - 1, $totalRows);

            $reader->setReadFilter(new ChunkReadFilter($startRow, $endRow));
            $spreadsheet = $reader->load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator($startRow, $endRow) as $row) {
                $cellIterator = $row->getCellIterator('A', 'U');
                $cellIterator->setIterateOnlyExistingCells(false);

                $document = [];
                foreach ($cellIterator as $cell) {
                    $column = $cell->getColumn();
                    if (!isset(self::COLUMN_MAP[$column])) {
                        continue;
                    }

                    $field = self::COLUMN_MAP[$column];
                    $value = $cell->getValue();

                    if ($field === 'quantity') {
                        $value = (float) $value;
                    }

                    $document[$field] = $value;
                }

                yield $document;
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
    }
}
