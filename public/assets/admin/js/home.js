/* ========================================================================

Home Js

=========================================================================
 */


"use strict";


/*======== Doucument Ready Function =========*/
jQuery(document).ready(function ($) {

    if ($('#sales_overview').length) {
      var onlineRevenueCanvas = $("#sales_overview").get(0).getContext("2d");

      var gradient1 = onlineRevenueCanvas.createLinearGradient(0, 0, 0, 300);
      gradient1.addColorStop(0, 'rgba(107, 207, 67, .2)');
      gradient1.addColorStop(1, 'rgba(255,255,255,.2)');
      var gradient2 = onlineRevenueCanvas.createLinearGradient(0, 0, 0, 500);
      gradient2.addColorStop(0, '#dcd7ff');
      gradient2.addColorStop(1, 'rgba(255,255,255,0)');

      var data = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [
          {
            label: 'Pre-order Sale',
            data: [550, 620, 530, 610 , 540, 770, 700, 800],
            borderColor: [
              '#6BCF43'
            ],
            borderWidth: 4,
            fill: true,
            backgroundColor: gradient1
          },
          {
            label: 'Sale',
            data: [750, 800, 670, 1000, 790 , 1020, 800, 920],
            borderColor: [
              '#6146D6'
            ],
            borderWidth: 4,
            fill: true,
            backgroundColor: gradient2
          }

        ]
      };
      var options = {
        scales: {
          yAxes: [{
            display: true,
            gridLines: {
              drawBorder: false,
              lineWidth: 1,
              color: "#f1f3f9",
              zeroLineColor: "#f1f3f9",
            },
            ticks: {
              min: 200,
              max: 1200,
              stepSize: 200,
              fontColor: "#979797",
              fontSize: 11,
              fontStyle: 400,
              padding: 15
            }
          }],
          xAxes: [{
            display: true,
            gridLines: {
              display: false,
              drawBorder: false,
              lineWidth: 1,
              color: "#e9e9e9",
            },
            ticks : {
              fontColor: "#979797",
              fontSize: 11,
              fontStyle: 400,
              padding: 15,
            }
          }]
        },
        legend: {
          display: false
        },
        elements: {
          point: {
            radius: 2,
          },
          line :{
            tension: .35
          }
        },
        stepsize: 1,
        layout : {
          padding : {
            top: 30,
            bottom : 0,
            left : 0,
            right: 0
          }
        }
      };
      var onlineRevenue = new Chart(onlineRevenueCanvas, {
        type: 'line',
        data: data,
        options: options
      });
      // document.getElementById('online-revenue-legend').innerHTML = onlineRevenue.generateLegend();
    }

    if ($('#sales_overview_dark').length) {
      var onlineRevenueCanvas = $("#sales_overview_dark").get(0).getContext("2d");

      var gradient1 = onlineRevenueCanvas.createLinearGradient(0, 0, 0, 350);
      gradient1.addColorStop(0, 'rgba(5, 541, 186, .5)');
      gradient1.addColorStop(1, 'rgba(0,0,0,0)');
      var gradient2 = onlineRevenueCanvas.createLinearGradient(0, 0, 0, 300);
      gradient2.addColorStop(0, 'rgba(98, 1, 237, .5)');
      gradient2.addColorStop(1, 'rgba(0,0,0,0)');

      var data = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [
          {
            label: 'Offline Revenue',
            data: [600, 620, 530, 610 , 540, 770, 700, 800],
            borderColor: [
              '#6BCF43'
            ],
            borderWidth: 4,
            fill: true,
            backgroundColor: gradient1
          },
          {
            label: 'Online Revenue',
            data: [600, 800, 670, 930, 790 , 1100, 800, 920],
            borderColor: [
              '#6201ed'
            ],
            borderWidth: 4,
            fill: true,
            backgroundColor: gradient2
          }

        ]
      };
      var options = {
        scales: {
          yAxes: [{
            display: true,
            gridLines: {
              drawBorder: false,
              lineWidth: 1,
              color: "rgba(46, 50, 74, .7)",
              zeroLineColor: "rgba(46, 50, 74, .7)",
            },
            ticks: {
              min: 200,
              max: 1200,
              stepSize: 200,
              fontColor: "#cacfe2",
              fontSize: 11,
              fontStyle: 400,
              padding: 15
            }
          }],
          xAxes: [{
            display: true,
            gridLines: {
              display: false,
              drawBorder: false,
              lineWidth: 1,
              color: "#e9e9e9",
            },
            ticks : {
              fontColor: "#cacfe2",
              fontSize: 11,
              fontStyle: 400,
              padding: 15,
            }
          }]
        },
        legend: {
          display: false
        },
        elements: {
          point: {
            radius: 2,
          },
          line :{
            tension: .35
          }
        },
        stepsize: 1,
        layout : {
          padding : {
            top: 30,
            bottom : 0,
            left : 0,
            right: 0
          }
        }
      };
      var onlineRevenue = new Chart(onlineRevenueCanvas, {
        type: 'line',
        data: data,
        options: options
      });
    } 

    $(function () {
        "use strict";

        var chartData = [
          [0, 50],
          [1, 50],
          [2, 55],
          [3, 55],
          [4, 57],
          [5, 53],
          [6, 49],
          [7, 45],
          [8, 54],
          [9, 52],
          [10, 50],
          [11, 45],
          [12, 41],
          [13, 37],
          [14, 54],
          [15, 49],
          [16, 52],
          [17, 52],
          [18, 52],
          [19, 50],
          [20, 49],
          [21, 45],
          [22, 45],
          [23, 45],
          [24, 58],
          [25, 57],
          [26, 56],
          [27, 54],
          [28, 54],
          [29, 54],
          [30, 50],
          [31, 49],
          [32, 48],
          [33, 47],
          [34, 46],
          [35, 48],
          [36, 49],
          [37, 50],
          [38, 51],
          [39, 53],
          [40, 55],
          [41, 59],
          [42, 65],
          [43, 71],
          [44, 76],
          [45, 74],
          [46, 74],
          [47, 74],
          [48, 74],
          [49, 67],
          [50, 60],
          [51, 58],
          [52, 57],
          [53, 56],
          [54, 59],
          [55, 64],
          [56, 67],
          [57, 65],
          [58, 67],
          [59, 70],
          [60, 68],
          [61, 66],
          [62, 64],
          [63, 59],
          [64, 57],
          [65, 59],
          [66, 56],
          [67, 53],
          [68, 45],
          [69, 50],
          [70, 55],
          [71, 57],
          [72, 62],
          [73, 65],
          [74, 63],
          [75, 64],
          [76, 68],
          [77, 65],
          [78, 60],
          [79, 62],
          [80, 59],
          [81, 57],
          [82, 55],
          [83, 54],
          [84, 50],
          [85, 55],
          [86, 53],
          [87, 50],
          [88, 48],
          [89, 49],
          [90, 50],
        ];

        function bgFlotData(num, val) {
          var data = [];
          for (var i = 0; i < num; ++i) {
            data.push([i, val]);
          }
          return data;
        }

        function bgFlotData(num, val) {
          var data = [];
          for (var i = 0; i < num; ++i) {
            data.push([i, val]);
          }
          return data;
        }

        var plot = $.plot(
          "#flotChart",
          [
            {
              data: chartData,
              color: "#6146d6",
              lines: {
                fillColor: "rgba(97, 70, 214, .12)",
              },
            },
          ],
          {
            series: {
              shadowSize: 0,
              lines: {
                show: true,
                lineWidth: 2,
                fill: true,
              },
            },
            grid: {
              borderWidth: 0,
              labelMargin: 8,
            },
            yaxis: {
              show: true,
              min: 0,
              max: 100,
              ticks: true,
              tickLength: 0,
              tickColor: "#000",
            },
            xaxis: {
              show: true,
              color: "#fff",
              tickColor: "#fff",
              ticks: [
                [0, "Jan"],
                [10, "Feb"],
                [20, "Mar"],
                [30, "Apr"],
                [40, "May"],
                [50, "Jun"],
                [60, "Jul"],
                [70, "Aug"],
                [80, "Sep"],
                [90, "Oct"],
              ],
              tickLength: 0,
            },
            yaxis: {
              show: true,
              color: "#fff",
              // tickColor: "#edf2fa",
              tickColor: "rgba(0,0,0,.05)",
              ticks: [
                [0, "0"],
                [20, "$1000"],
                [40, "$2000"],
                [60, "$3000"],
                [80, "$4000"],
              ],
            },
          }
        );
      });

    var recentSalesData = {
        labels: ["Jan", "Feb", "Mar", "Apr"],
        datasets: [{
                label: 'Week 1 Sales',
                data: [4463, 6073, 4261, 1264],
                backgroundColor: [
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                ],
                borderColor: [
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                    'rgba(97,70,214, 1)',
                ],
                borderWidth: 2,
                fill: false
            },
            {
                label: 'Week 2 Sales',
                data: [9546, 2631, 2753, 1642],
                backgroundColor: [
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                ],
                borderColor: [
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                    'rgba(107, 207, 67, 1)',
                ],
                borderWidth: 2,
                fill: false
            },
            {
                label: 'Week 3 Sales',
                data: [1853, 3124, 5321, 5387],
                backgroundColor: [
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                ],
                borderColor: [
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                    'rgba(255, 166, 74, 1)',
                ],
                borderWidth: 2,
                fill: false
            },
            {
                label: 'Week 4 Sales',
                data: [4287, 1316, 8482, 1627],
                backgroundColor: [
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                ],
                borderColor: [
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                    'rgba(224, 224, 224, 1)',
                ],
                borderWidth: 2,
                fill: false
            }
        ],
    };

    var recentSalesOption = {
        scales: {
            xAxes: [{
                position: 'bottom',
                display: true,
                gridLines: {
                    display: false,
                    drawBorder: true,
                },
                ticks: {
                    display: false //this will remove only the label
                }
            }],
            yAxes: [{
                display: false,
                gridLines: {
                    drawBorder: true,
                    display: false,
                },
            }]
        },
        legend: {
            display: false
        },
        legendCallback: function(chart) {
            var text = [];
            text.push('<div class="row">');
            for (var i = 0; i < chart.data.datasets.length; i++) {
                text.push('<div class="col-sm-6 col mr-3 ml-3 ml-sm-0 mr-sm-0 pr-md-0"><div class="row mb-3 align-items-center"><div class="col-md-2"><span class="legend-label" style="background-color:' + chart.data.datasets[i].backgroundColor[i] + '"></span></div><div class="col-md-9 pl-md-2"><h3 class="mb-0">$ ' + chart.data.datasets[i].data[i] + '</h3></div><div class="col-sm-12"><p class="text-muted">' + chart.data.datasets[i].label + '</p></div></div>');
                text.push('</div>');
            }
            text.push('</div>');
            return text.join("");
        },
        tooltips: {
            backgroundColor: 'rgba(31, 59, 179, 1)',
        }

    };

    if ($("#recentSalesDetail").length) {
        var barChartCanvas = $("#recentSalesDetail").get(0).getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var barChart = new Chart(barChartCanvas, {
            type: 'horizontalBar',
            data: recentSalesData,
            options: recentSalesOption
        });
    }

    if ($("#customers-chart").length) {
      var customersChartCanvas = $("#customers-chart").get(0).getContext("2d");
      var customersChart = new Chart(customersChartCanvas, {
        type: 'bar',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May","Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
              label: 'Users',
              data: [34, 19, 16, 22, 33, 25, 40, 70, 85, 50, 23, 20],
              backgroundColor: '#6146D6'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              display: false,
              gridLines: {
                drawBorder: false,
                color: '#6146D6',
                zeroLineColor: '#6146D6'
              },
              ticks: {
                display: false,
                fontColor: "#9fa0a2",
                padding: 0,
                stepSize: 20,
                min: 0,
                max: 100
              }
            }],
            xAxes: [{
              display: false,
              stacked: false,
              categoryPercentage: 1,
              ticks: {
                display: false,
                beginAtZero: false,
                display: true,
                padding: 10,
                fontSize: 11
              },
              gridLines: {
                color: "rgba(0, 0, 0, 0)",
                display: false
              },
              position: 'bottom',
              barPercentage: 0.7
            }]
          },
          legend: {
            display: false
          },
          elements: {
            point: {
              radius: 0
            }
          }
        }
      });
    }

    var piedata = [
        {label: 'Very Satisfied', data: [[1, 25]], color: '#654FC6'},
        {label: 'Satisfied', data: [[1, 38]], color: '#6DD244'},
        {label: 'Not Satisfied', data: [[1, 20]], color: '#FFA549'},
        {label: 'Very Unsatisfied', data: [[1, 15]], color: '#39DA8A'}
    ];

    $.plot('#sales_country', piedata, {
        series: {
            pie: {
                show: true,
                radius: 1,
                innerRadius: 0.6,
                label: {
                    show: true,
                    radius: 3.2 / 4,
                    formatter: textFormatter,
                }
            }
        },
        grid: {
            hoverable: false,
            clickable: false
        },
        legend: {
            show: false
        }
    });

    function textFormatter(label, series) {
        return '<div style="font-size:10px; font-weight:700; text-align:center; color:#ffff;">' + Math.round(series.percent) + '%</div>';
    }
});
/*======== End Doucument Ready Function =========*/