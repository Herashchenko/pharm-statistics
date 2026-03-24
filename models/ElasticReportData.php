<?php

namespace app\models;

use yii\elasticsearch\ActiveRecord;

class ElasticReportData extends ActiveRecord
{
    public static function index()
    {
        return 'report_data';
    }

    public static function type()
    {
        return '_doc';
    }

    public function attributes()
    {
        return [
            'region',
            'product',
            'quantity',
            'company',
            'city',
        ];
    }

    public static function mapping()
    {
        return [
            'properties' => [
                'region'   => ['type' => 'keyword'],
                'product'  => ['type' => 'keyword'],
                'quantity' => ['type' => 'double'],
                'company'  => ['type' => 'keyword'],
                'city'     => ['type' => 'keyword'],
            ],
        ];
    }
}
