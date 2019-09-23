<?php
require_once('./lib/function.php');

$json_root_dir = './json';
$files = scandir($json_root_dir);
//var_dump($files);
$count = 0;
$total = 0;
foreach ($files as $file_name) {
    $total++;
    $file_path = $json_root_dir.'/'.$file_name;
    $file_content = file_get_contents($file_path);
    //echo $file_content;
    $file_json = json_decode($file_content, false);
    //var_dump($file_json);
    $copyright = $file_json->copyright;
    $date = $file_json->date;
    $link = $file_json->link;

    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, -2);
    $folder = 'images';
    $name_start_pos = strpos($link, '/th?id=');
    if ($name_start_pos === false) {
        $name_start_pos = strrpos($link, '/') + 1;
    } else {
       $name_start_pos += 7;
    }
    $name_end_pos = strpos($link, '_1920x1080.jpg');
    $downloan_image_name = substr($link, $name_start_pos, $name_end_pos - $name_start_pos);
    $response = request($link);
    $image_path = 'tmp/'.$downloan_image_name.'.jpg';
    
    $image_result = check_image_and_save($image_path, $response);
    if ($image_result !== false) {
        $count++;
    } else {
        echo $link.'
';
    }
//    echo $image_path.'
//';
//    echo $link.'
//';
//    echo $download_cmd.'
//';
//    echo '-------------------
//';
}
echo 'total:'.$total.' count:'.$count.'
';
