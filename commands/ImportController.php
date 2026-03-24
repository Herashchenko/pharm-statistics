<?php

namespace app\commands;

use app\services\PharmStatisticsImporter;
use app\services\SpreadsheetReader;
use yii\console\Controller;
use yii\console\ExitCode;

class ImportController extends Controller
{
    public function actionIndex(string $filePath): int
    {
        if (!file_exists($filePath)) {
            $this->stderr("File not found: {$filePath}\n");
            return ExitCode::DATAERR;
        }

        $reader = new SpreadsheetReader($filePath);
        $this->stdout("Reading file: {$filePath}\n");
        $this->stdout("Total rows: {$reader->getTotalRows()}\n");

        $importer = new PharmStatisticsImporter($reader);
        $totalInserted = $importer->import(function (int $count) {
            $this->stdout("Inserted: {$count}\r");
        });

        $this->stdout("\nDone. Total documents inserted: {$totalInserted}\n");

        return ExitCode::OK;
    }
}
