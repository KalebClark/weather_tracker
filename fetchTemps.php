<?php
require('CURL.php');
require('UTIL.php');
require('secure.php');
$util = new Util();

$sql = new mysqli($mysql['host'], $mysql['user'], $mysql['passwd'], "weather_tracking");
if($sql->connect_error) {
    die("Connection Failed: " . $sql->connect_error);
} 
$location = $_GET['loc'];

$city_query = "SELECT location_id, owm_id FROM locations WHERE name = '$location'";
$loc_data   = $sql->query($city_query);
$loc_assoc  = $loc_data->fetch_assoc();
$loc_id     = $loc_assoc['location_id'];

$curl = new Curly();
$data = $curl->getURL("http://api.openweathermap.org/data/2.5/forecast?id=".$loc_assoc['owm_id']."&units=imperial");
$data_array = json_decode($data, 1);

foreach($data_array['list'] as $day) {
    $dt = $day['dt'] * 1000;
    print $dt."<br/>";
    print $day['main']['temp_max']."<br/>";
    //$util->dumper($day);
}
//$util->dumper($data_array);
exit;

$query = "
    SELECT  weather.temp
        ,   DATE_FORMAT(create_date, '%Y-%m-%d %H:%i:00') AS timekey
    FROM weather
    WHERE location_id = '$loc_id'
    ORDER BY timekey DESC
";

$rows = $sql->query($query);
$all_rows = Array();

while($row = $rows->fetch_assoc()) {
    $all_rows[] = $row;
}

// Calc dates for midnight
$latest_date = $all_rows[0]['timekey'];
$stop_date = new DateTime($latest_date);
$midnight = $stop_date->format('Y-m-d 12:59:59');

$unix_ld = date("U", strtotime($latest_date));
$unix_mn = date("U", strtotime($midnight));
//print $unix_ld."<br/>";
//print $unix_mn."<br/>";

$new_rows = Array();
$i = 0;
while($unix_ld <= $unix_mn) {
    $unix_ld += 900;
    $new_rows[$i]['timekey'] = date('Y-m-d H:i:s', $unix_ld);
    $new_rows[$i]['temp'] = 100;
    $i++;
}
//print_r($new_rows);
//exit;
//print $midnight;
//print "<br>";

//exit;

$new_rows = array_reverse($new_rows);
$all_rows = array_merge($new_rows, $all_rows);
$all_rows = array_reverse($all_rows);     # Reverse Array


// Everything is gathered. Now process
$data = "[";
foreach($all_rows as $row) {
    $date = date("U", strtotime($row['timekey']));
    $data .= "[";
    $data .= ($date * 1000).", ";
//    print $date."-".$row['temp']."<br/>";
    $data .= $row['temp']."";
    $data .= "],";
}
$data = preg_replace('/,$/', ']', $data);

print $data;
?>

