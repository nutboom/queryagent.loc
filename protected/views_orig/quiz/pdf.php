<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
    <script type="application/javascript">
        $(function () {
            $('#container').highcharts({
                chart: {
                    type: 'column',
                    width:800
                },
                title: {
                    text: 'Monthly Average Rainfall'
                },
                xAxis: {
                    labels: {
                        enabled: false
                    }
                },
                tooltip: {
                    headerFormat: '<table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                series: [{
                    name:"Yes",
                    data: [49.9],
                    dataLabels: {
                        enabled: true
                    }
                },
                    {
                        name:"No",
                        data:[86],
                        dataLabels: {
                            enabled: true
                        }
                    }],
                legend:{
                    enabled:true,
                    verticalAlign: 'middle',
                    align:'right',
                    layout:'vertical',
                    width:300
                }

            });
        });


    </script>



</head>

<body>



    <div id="container" style="height: 300px; margin-top: 1em"></div>
    <button id="button">Export chart</button>
</body>

</html>