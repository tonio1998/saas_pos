import $ from 'jquery';
import Chart from 'chart.js/auto';

const chartData = {
    daily: {
        labels: [
            '1', '2', '3', '4', '5', '6', '7',
            '8', '9', '10', '11', '12', '13', '14',
            '15', '16', '17', '18', '19', '20',
            '21', '22', '23', '24', '25', '26',
            '27', '28', '29', '30'
        ],
        data: [
            12500, 14200, 9800, 15300, 17500,
            18800, 20500, 17200, 19400, 21300,
            22500, 24700, 19800, 21500, 23600,
            24100, 25800, 26700, 27400, 28900,
            30100, 31500, 32200, 33800, 35400,
            36100, 37800, 39200, 40500, 42500
        ]
    },

    monthly: {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        data: [
            325000,
            358000,
            412000,
            398000,
            445000,
            520000,
            0,
            0,
            0,
            0,
            0,
            0
        ]
    },

    annual: {
        labels: [
            '2022',
            '2023',
            '2024',
            '2025',
            '2026'
        ],
        data: [
            2500000,
            3200000,
            4500000,
            5800000,
            6200000
        ]
    }
};

let salesChart = null;

function renderChart(type = 'daily') {
    const canvas = document.getElementById('salesChart');

    if (!canvas || !chartData[type]) {
        return;
    }

    const dataset = chartData[type];

    if (salesChart) {
        salesChart.destroy();
    }

    salesChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: dataset.labels,
            datasets: [
                {
                    label: 'Sales',
                    data: dataset.data,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label(context) {
                            return '₱' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return '₱' + Number(value).toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

$(document).ready(function () {
    if (!$('#salesChart').length) {
        return;
    }

    renderChart('daily');

    $('.chart-filter').on('click', function () {
        $('.chart-filter')
            .removeClass('active btn-success')
            .addClass('btn-outline-success');

        $(this)
            .addClass('active btn-success')
            .removeClass('btn-outline-success');

        renderChart($(this).data('type'));
    });
});
