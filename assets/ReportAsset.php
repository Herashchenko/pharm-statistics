<?php

namespace app\assets;

use yii\web\AssetBundle;

class ReportAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules';

    public $css = [
        'datatables.net-bs5/css/dataTables.bootstrap5.min.css',
    ];

    public $js = [
        'datatables.net/js/dataTables.min.js',
        'datatables.net-bs5/js/dataTables.bootstrap5.min.js',
        'highcharts/highcharts.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];
}
