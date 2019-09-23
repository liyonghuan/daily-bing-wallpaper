<?php
require_once('./lib/function.php');

$files = scandir('html');
define('ONE_DAY', 24 * 60 * 60);
foreach ($files as $file) {
    $path = 'html/'.$file;
    //echo $path;
    $input = file_get_contents($path);
    //echo $input;
    $start = 0;
    
    $date_start_pos = strpos($input, 'date">', $start);
    //echo $date_start_pos;
    if ($date_start_pos === false) {
        continue;
    }
    $date_start_pos += 6;
    $date_end_pos = strpos($input, '</strong>', $date_start_pos);
    //echo $date_end_pos;
    $date = substr($input, $date_start_pos, $date_end_pos - $date_start_pos);
    $date = date('Ymd', strtotime($date) - ONE_DAY);
    echo $date.'
';
    //echo $date;
    $search_start_pos = strpos($input, '<a href="', $date_end_pos) + 9;
    //echo $search_start_pos;
    $search_end_pos = strpos($input, '" ', $search_start_pos);
    
    $search = substr($input, $search_start_pos, $search_end_pos - $search_start_pos);
    $copyright_start_pos = strpos($input, 'id="title">', $search_end_pos) + 11;
    $copyright_end_pos = strpos($input, '</span>', $copyright_start_pos);
    $copyright = substr($input, $copyright_start_pos, $copyright_end_pos - $copyright_start_pos);
    $link_start_pos = strpos($input, '<a href="', $copyright_end_pos) + 9;
    $link_end_pos = strpos($input, '" ', $link_start_pos);
    $link = substr($input, $link_start_pos, $link_end_pos - $link_start_pos);
    
    $obj = new stdClass();

    $obj->link = $link;
    $obj->date = $date;
    $obj->copyright = $copyright;
    $obj->search = $search;

    $json = json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    check_path_and_save('./json/'.$date.'.json', $json);
}
