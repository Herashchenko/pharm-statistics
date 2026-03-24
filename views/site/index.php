<?php

/** @var yii\web\View $this */

use app\assets\ReportAsset;

ReportAsset::register($this);
$this->registerJsFile('@web/js/dashboard.js', ['depends' => [ReportAsset::class]]);

$this->title = 'Фарм-статистика — Dashboard';
?>

<div class="site-index">

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h6 class="card-title">Загальний обсяг відвантажень</h6>
                    <h2 class="mb-0" id="kpi-total-quantity">—</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h6 class="card-title">Унікальних товарів</h6>
                    <h2 class="mb-0" id="kpi-unique-products">—</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h6 class="card-title">Охоплення регіонів</h6>
                    <h2 class="mb-0" id="kpi-unique-regions">—</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h6 class="card-title">Активних міст</h6>
                    <h2 class="mb-0" id="kpi-unique-cities">—</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div id="chart-regions" class="chart-container"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div id="chart-products" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="dashboard-table" class="table table-striped table-full-width">
                        <thead>
                            <tr>
                                <th>Область</th>
                                <th>Товар</th>
                                <th>Кількість</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
