<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class PharmStatistics extends ActiveRecord
{
    public static function collectionName()
    {
        return 'pharm_statistics';
    }

    public function attributes()
    {
        return [
            '_id',
            'company',
            'region',
            'city',
            'invoice_date',
            'delivery_address',
            'legal_address',
            'client',
            'client_code',
            'client_department_code',
            'client_okpo',
            'license',
            'license_expiry_date',
            'product_code',
            'barcode',
            'product',
            'morion_code',
            'unit',
            'manufacturer',
            'supplier',
            'quantity',
            'warehouse',
        ];
    }

    public function rules()
    {
        return [
            [['company', 'region', 'city', 'client', 'product'], 'required'],
            [['quantity'], 'number'],
            [
                [
                    'company', 'region', 'city', 'invoice_date',
                    'delivery_address', 'legal_address', 'client',
                    'client_code', 'client_department_code', 'client_okpo',
                    'license', 'license_expiry_date', 'product_code',
                    'barcode', 'product', 'morion_code', 'unit',
                    'manufacturer', 'supplier', 'warehouse',
                ],
                'safe',
            ],
        ];
    }
}
