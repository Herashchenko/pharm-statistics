<?php

namespace app\services;

use Yii;

class PharmStatisticsImporter
{
    private const BATCH_SIZE = 500;

    private SpreadsheetReader $reader;

    public function __construct(SpreadsheetReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Imports rows from spreadsheet into MongoDB pharm_statistics collection.
     *
     * @param callable|null $onProgress Called with total inserted count after each batch.
     * @return int Total number of inserted documents.
     */
    public function import(?callable $onProgress = null): int
    {
        $collection = Yii::$app->mongodb->getCollection('pharm_statistics');
        $collection->remove([]);
        $batch = [];
        $totalInserted = 0;

        foreach ($this->reader->readRows() as $document) {
            $batch[] = $document;

            if (count($batch) >= self::BATCH_SIZE) {
                $collection->batchInsert($batch);
                $totalInserted += count($batch);
                $batch = [];

                if ($onProgress !== null) {
                    $onProgress($totalInserted);
                }
            }
        }

        if (!empty($batch)) {
            $collection->batchInsert($batch);
            $totalInserted += count($batch);

            if ($onProgress !== null) {
                $onProgress($totalInserted);
            }
        }

        return $totalInserted;
    }
}
