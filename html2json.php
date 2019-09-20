<?php
$dir = './html';
for ($i = 1; $i < 1211; $i++) {
    $path = $dir.'/'.$i.'.html';
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
    $date--;
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
    file_put_contents('./json/'.$date.'.json', $json);
}
