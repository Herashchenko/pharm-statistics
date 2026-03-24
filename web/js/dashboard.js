$(function () {
    $.getJSON('/site/get-kpi', renderKpi);
    $.getJSON('/site/get-sales-by-region', renderRegionsChart);
    $.getJSON('/site/get-top-products', renderProductsChart);
    $.getJSON('/site/get-table-data', renderTable);

    function formatNumber(val) {
        return parseFloat(val).toLocaleString('uk-UA', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    function renderKpi(kpi) {
        $('#kpi-total-quantity').text(formatNumber(kpi.totalQuantity));
        $('#kpi-unique-products').text(formatNumber(kpi.uniqueProducts));
        $('#kpi-unique-regions').text(formatNumber(kpi.uniqueRegions));
        $('#kpi-unique-cities').text(formatNumber(kpi.uniqueCities));
    }

    function renderRegionsChart(salesByRegion) {
        var seriesData = salesByRegion.map(function (item) {
            return {name: item.name, y: item.quantity};
        });

        Highcharts.chart('chart-regions', {
            chart: {type: 'pie'},
            title: {text: 'Частка продажів по областях'},
            tooltip: {
                pointFormat: '<b>{point.y:,.2f}</b> ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.percentage:.1f}%',
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 4
                        }
                    }
                }
            },
            legend: {enabled: false},
            series: [{
                name: 'Кількість',
                data: seriesData
            }],
            credits: {enabled: false}
        });
    }

    function renderProductsChart(topProducts) {
        var categories = topProducts.map(function (item) { return item.name; });
        var values = topProducts.map(function (item) { return item.quantity; });

        Highcharts.chart('chart-products', {
            chart: {type: 'bar'},
            title: {text: 'ТОП-10 найпопулярніших товарів'},
            xAxis: {
                categories: categories,
                labels: {
                    style: {fontSize: '11px'}
                }
            },
            yAxis: {
                title: {text: 'Сумарна кількість'},
                labels: {
                    formatter: function () {
                        return this.value.toLocaleString('uk-UA');
                    }
                }
            },
            tooltip: {
                pointFormat: 'Кількість: <b>{point.y:,.2f}</b>'
            },
            legend: {enabled: false},
            series: [{
                name: 'Кількість',
                data: values,
                colorByPoint: true
            }],
            credits: {enabled: false}
        });
    }

    function renderTable(tableData) {
        $('#dashboard-table').DataTable({
            data: tableData,
            columns: [
                {data: 'region'},
                {data: 'product'},
                {
                    data: 'quantity',
                    render: function (val) {
                        return formatNumber(val);
                    }
                }
            ],
            order: [[2, 'desc']],
            pageLength: 25,
            language: {
                search: 'Пошук:',
                lengthMenu: 'Показати _MENU_ записів',
                info: 'Записи _START_–_END_ з _TOTAL_',
                infoEmpty: 'Немає записів',
                infoFiltered: '(відфільтровано з _MAX_)',
                paginate: {
                    first: 'Перша',
                    last: 'Остання',
                    next: 'Далі',
                    previous: 'Назад'
                },
                zeroRecords: 'Нічого не знайдено'
            }
        });
    }
});
