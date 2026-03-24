<?php

namespace app\commands;

use app\services\ElasticTransferService;
use yii\console\Controller;
use yii\console\ExitCode;

class ElasticTransferController extends Controller
{
    public function actionIndex(): int
    {
        $service = new ElasticTransferService();

        if ($service->ensureIndex()) {
            $this->stdout("Created index 'report_data'\n");
        }

        $this->stdout("Documents in MongoDB: {$service->getTotalInMongo()}\n");

        $transferred = $service->transfer(function (int $current, int $total) {
            $this->stdout("Transferred: {$current} / {$total}\r");
        });

        $this->stdout("\nDone. Total transferred: {$transferred}\n");

        return ExitCode::OK;
    }
}
