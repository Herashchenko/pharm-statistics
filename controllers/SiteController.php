<?php

namespace app\controllers;

use app\services\ReportService;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionGetKpi(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return (new ReportService())->getKpi();
    }

    public function actionGetSalesByRegion(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return (new ReportService())->getSalesByRegion();
    }

    public function actionGetTopProducts(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return (new ReportService())->getTopProducts();
    }

    public function actionGetTableData(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return (new ReportService())->getAggregatedData();
    }
}
