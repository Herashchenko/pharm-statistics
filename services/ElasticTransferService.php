<?php

namespace app\services;

use app\models\ElasticReportData;
use app\models\PharmStatistics;
use Yii;

class ElasticTransferService
{
    private const BATCH_SIZE = 500;

    /**
     * Ensures the Elasticsearch index exists with proper mapping.
     *
     * @return bool
     */
    public function ensureIndex(): bool
    {
        $command = Yii::$app->elasticsearch->createCommand();
        $indexName = ElasticReportData::index();

        if ($command->indexExists($indexName)) {
            return false;
        }

        $command->createIndex($indexName, [
            'mappings' => ElasticReportData::mapping(),
        ]);

        return true;
    }

    /**
     * Transfers documents from MongoDB to Elasticsearch.
     *
     * @param callable|null $onProgress Called with (int $transferred, int $total) after each batch.
     * @return int Total number of transferred documents.
     */
    public function transfer(?callable $onProgress = null): int
    {
        $totalCount = PharmStatistics::find()->count();
        $transferred = 0;
        $batch = [];

        $bulkCommand = $this->createBulkCommand();

        foreach (PharmStatistics::find()->each(self::BATCH_SIZE) as $doc) {
            $bulkCommand->addAction(
                ['index' => ['_index' => ElasticReportData::index(), '_id' => (string) $doc->_id]],
                [
                    'region'   => $doc->region,
                    'product'  => $doc->product,
                    'quantity' => (float) $doc->quantity,
                    'company'  => $doc->company,
                    'city'     => $doc->city,
                ]
            );

            $transferred++;

            if ($transferred % self::BATCH_SIZE === 0) {
                $bulkCommand->execute();
                $bulkCommand = $this->createBulkCommand();

                if ($onProgress !== null) {
                    $onProgress($transferred, $totalCount);
                }
            }
        }

        if ($transferred % self::BATCH_SIZE !== 0) {
            $bulkCommand->execute();

            if ($onProgress !== null) {
                $onProgress($transferred, $totalCount);
            }
        }

        return $transferred;
    }

    public function getTotalInMongo(): int
    {
        return PharmStatistics::find()->count();
    }

    private function createBulkCommand(): \yii\elasticsearch\BulkCommand
    {
        return Yii::$app->elasticsearch->createBulkCommand();
    }
}
