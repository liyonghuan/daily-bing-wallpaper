<?php
require_once('./lib/function.php');

$file_lists = scandir('tmp');

$total = 0;
$count = 0;

foreach ($file_lists as $file) {
    $total++;
    
    $name = substr($file, 0, -4);
    echo $name.'
';
    $prefix = 'OHR.';
    //if ($prefix != substr($name, 0, 4)) {
    //    $name = $prefix.$name;
    //}
    $image_url = 'https://www.bing.com/th?id='.$name.'_tmb.jpg';
    $response = request($image_url, 2);
    if ($response === false) {
        $name = $prefix.$name;
        $image_url = 'https://www.bing.com/th?id='.$name.'_tmb.jpg';
        $response = request($image_url, 2);

    }
    if ($response === false) {
        continue;
    }
    $path = 'tmb/'.$name.'_tmb.jpg';
    $result = check_image_and_save($path, $response);
    if ($result !== false) {
        $count++;
    }
}
echo 'total:'.$total.' count:'.$count.'
';
