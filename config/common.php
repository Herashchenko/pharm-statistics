<?php

return [
    'mongodb' => [
        'class' => \yii\mongodb\Connection::class,
        'dsn' => getenv('MONGODB_DSN') ?: 'mongodb://mongodb:27017/pharm_statistics',
    ],
    'elasticsearch' => [
        'class' => \yii\elasticsearch\Connection::class,
        'nodes' => [
            ['http_address' => getenv('ELASTICSEARCH_HOST') ?: 'elasticsearch:9200'],
        ],
        'dslVersion' => 7,
    ],
];
