<html>
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="http://code.highcharts.com/stock/highstock.js"></script>
    <script src="http://code.highcharts.com/stock/modules/exporting.js"></script>



    </head>
    <body>
    <script type="text/javascript">
	$(function () {
    var seriesOptions = [],
        seriesCounter = 0,
        names = ['Sacramento', 'Ukiah', 'Willits'],
        // create the chart when all data is loaded
        createChart = function () {

            $('#container').highcharts('StockChart', {

                rangeSelector: {
                    allButtonsEnabled: true,
                    selected: 4
                },

                yAxis: {
                    title: {
                        text: 'Too Hot'
                    },
                    plotLines: [{
                        value: 100.00,
                        color: 'red',
                        dashStyle: 'shortdash',
                        width: 2,
                        label: {
                            text: 'When its too damn hot'    
                        }
                    }, {
                        value: 65.00,
                        color: 'blue',
                        dashStyle: 'shortdash',
                        width: 2,
                        label: {
                            text: 'When its too damn cold'    
                        }
                        
                    }],


                },

/*
                yAxis: {
                    labels: {
                        formatter: function () {
                            return (this.value > 0 ? ' + ' : '') + this.value + '%';
                        }
                    },

                    plotLines: [{
                        value: 0,
                        width: 2,
                        color: 'silver'
                    }]
                },

                plotOptions: {
                    series: {
                        compare: 'percent'
                    }
                },

                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                    valueDecimals: 2
                },
*/

                series: seriesOptions
            });
        };

    $.each(names, function (i, name) {

        //$.getJSON('http://abraxxus.net/static/getData.php?city=' + name,    function (data) {
        $.getJSON('http://abraxxus.net/static/fetchTemps.php?loc=' + name,    function (data) {
        //$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=' + name.toLowerCase() + '-c.json&callback=?',    function (data) {
            console.log(data);

            seriesOptions[i] = {
                name: name,
                data: data
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            seriesCounter += 1;

            if (seriesCounter === names.length) {
                createChart();
            }
        });
    });
});

    </script>
    
    <div id="container" style="height: 400px; min-width: 310px"></div>
    </body>

</html>
