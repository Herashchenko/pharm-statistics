<?php

namespace app\services;

use app\models\ElasticReportData;

class ReportService
{
    public function getKpi(): array
    {
        $query = ElasticReportData::find()
            ->limit(0)
            ->addAggregate('total_quantity', [
                'sum' => ['field' => 'quantity'],
            ])
            ->addAggregate('unique_products', [
                'cardinality' => ['field' => 'product'],
            ])
            ->addAggregate('unique_regions', [
                'cardinality' => ['field' => 'region'],
            ])
            ->addAggregate('unique_cities', [
                'cardinality' => ['field' => 'city'],
            ]);

        $result = $query->search();
        $aggs = $result['aggregations'] ?? [];

        return [
            'totalQuantity'  => $aggs['total_quantity']['value'] ?? 0,
            'uniqueProducts' => $aggs['unique_products']['value'] ?? 0,
            'uniqueRegions'  => $aggs['unique_regions']['value'] ?? 0,
            'uniqueCities'   => $aggs['unique_cities']['value'] ?? 0,
        ];
    }

    public function getSalesByRegion(): array
    {
        $query = ElasticReportData::find()
            ->limit(0)
            ->addAggregate('sales_by_region', [
                'terms' => [
                    'field' => 'region',
                    'size'  => 50,
                    'order' => ['total_quantity' => 'desc'],
                ],
                'aggs' => [
                    'total_quantity' => [
                        'sum' => ['field' => 'quantity'],
                    ],
                ],
            ]);

        $result = $query->search();
        $rows = [];

        foreach ($result['aggregations']['sales_by_region']['buckets'] ?? [] as $bucket) {
            $rows[] = [
                'name'     => $bucket['key'],
                'quantity' => $bucket['total_quantity']['value'],
            ];
        }

        return $rows;
    }

    public function getTopProducts(): array
    {
        $query = ElasticReportData::find()
            ->limit(0)
            ->addAggregate('top_10_products', [
                'terms' => [
                    'field' => 'product',
                    'size'  => 10,
                    'order' => ['total_quantity' => 'desc'],
                ],
                'aggs' => [
                    'total_quantity' => [
                        'sum' => ['field' => 'quantity'],
                    ],
                ],
            ]);

        $result = $query->search();
        $rows = [];

        foreach ($result['aggregations']['top_10_products']['buckets'] ?? [] as $bucket) {
            $rows[] = [
                'name'     => $bucket['key'],
                'quantity' => $bucket['total_quantity']['value'],
            ];
        }

        return $rows;
    }

    /**
     * Two-level aggregation: region → product → sum(quantity).
     */
    public function getAggregatedData(): array
    {
        $query = ElasticReportData::find()
            ->limit(0)
            ->addAggregate('regions', [
                'terms' => [
                    'field' => 'region',
                    'size'  => 10000,
                ],
                'aggs' => [
                    'products' => [
                        'terms' => [
                            'field' => 'product',
                            'size'  => 10000,
                        ],
                        'aggs' => [
                            'total_quantity' => [
                                'sum' => ['field' => 'quantity'],
                            ],
                        ],
                    ],
                ],
            ]);

        $result = $query->search();

        return $this->flattenAggregations($result['aggregations'] ?? []);
    }

    private function flattenAggregations(array $aggregations): array
    {
        $rows = [];

        foreach ($aggregations['regions']['buckets'] ?? [] as $regionBucket) {
            $region = $regionBucket['key'];

            foreach ($regionBucket['products']['buckets'] as $productBucket) {
                $rows[] = [
                    'region'   => $region,
                    'product'  => $productBucket['key'],
                    'quantity' => $productBucket['total_quantity']['value'],
                ];
            }
        }

        return $rows;
    }
}
