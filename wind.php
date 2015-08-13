<html>
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/highcharts-more.js"></script>
    <script src="http://code.highcharts.com/modules/data.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>



    </head>
    <body>
    <?php
    require('secure.php');
    $sql = new mysqli($mysql['host'], $mysql['user'], $mysql['passwd'], "weather_tracking");
    if($sql->connect_error) {
        die("Connection Failed: " . $sql->connect_error);
    } 

    $locQuery = "SELECT * FROM locations";
    $locRes   = $sql->query($locQuery);

    $degrees = Array(
        'N'     => Array('0',       '22.5'),
        'NNE'   => Array('22.6',    '45'),
        'NE'    => Array('45.1',    '67.5'),
        'ENE'   => Array('67.6',    '90'),
        'E'     => Array('90.1',    '112.5'),
        'ESE'   => Array('112.6',   '135'),
        'SE'    => Array('135.1',   '157.5'),
        'SSE'   => Array('157.6',   '180'),
        'S'     => Array('180.1',   '202.5'),
        'SSW'   => Array('202.6',   '225'),
        'SW'    => Array('225.1',   '247.5'),
        'WSW'   => Array('247.6',   '270'),
        'W'     => Array('270.1',   '292.5'),
        'WNW'   => Array('292.6',   '315'),
        'NW'    => Array('315.1',   '337.5'),
        'NNW'   => Array('337.6',   '360')
    );
?>
    
    </body>
    <?php
    while($locRow  = $locRes->fetch_assoc()) {
        //print_r($locRow);
        showWindChart($locRow['location_id'], $locRow['name']);
    }

    ?>

</html>
<?php
function showWindChart($location, $locname) {
    global $degrees;
    global $sql;

    $categories = "[";
    $data = "[";

    foreach($degrees as $key => $deg) {
        $categories .= "'$key', ";
        $query = "
            SELECT  AVG(speed) as speed
                ,   deg
            FROM wind
            WHERE deg
                BETWEEN '$deg[0]' AND '$deg[1]'
                AND location_id = '$location'
        ";
        //print $query."<br/>";

        $res = $sql->query($query);
        $rows = $res->fetch_assoc();
        if($rows['speed'] <= 0) { $rows['speed'] = 0; }
        $data .= "$rows[speed], ";
        //print "<pre>";
        //print_r($rows);
        //print "</pre>";
    }
    $categories = preg_replace("/, $/", "]", $categories);
    $data = preg_replace("/, $/", "]", $data);
    //print_r($categories);
    //print_r($data);

    ?>
    <script type="text/javascript">
        $(function () {

    $('#container-<?php echo $location;?>').highcharts({

        chart: {
            polar: true,
            type: 'bar'
        },

        title: {
            text: 'Average Wind Speed & Direction<br/><?=$locname;?>',
            x: -80
        },

        pane: {
            size: '80%'
        },

        xAxis: {
            categories: <? print $categories;?>,
            tickmarkPlacement: 'on',
            lineWidth: 0
        },

        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0
        },

        tooltip: {
            shared: true,
            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}Mph</b><br/>'
        },

        legend: {
            align: 'right',
            verticalAlign: 'top',
            y: 70,
            layout: 'vertical'
        },

        series: [{
            name: 'Wind',
            //data: [1, 2, 3, 4, 5, 6,7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
            data: <? print $data; ?>,
            pointPlacement: 'off'
        }]

    });
});
    </script>
    <div id="container-<?php echo $location;?>" style="height: 400px; min-width: 310px"></div>
<? } // End Function ?>
