<?php
require_once('./lib/function.php');

$json_root_dir = './json';
$api_root_dir = './api';
$files = scandir($json_root_dir);
//var_dump($files);
//return;
$tmp_count = 0;
foreach ($files as $file_name) {
    if ($file_name == '.' || $file_name == '..') continue;
    $file_path = $json_root_dir.'/'.$file_name;
    $file_content = file_get_contents($file_path);
    if ($file_content === false) continue;
    //echo $file_content;
    $file_json = json_decode($file_content, false);
    //var_dump($file_json);
    $copyright = $file_json->copyright;
    $date = $file_json->date;
    $link = $file_json->link;

    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, -2);
    $folder = 'cn/'.$year.'/'.$month;
    $name_start_pos = strpos($link, '/th?id=');
    if ($name_start_pos === false) {
        $name_start_pos = strrpos($link, '/') + 1;
    } else {
       $name_start_pos += 7;
    }
    $name_end_pos = strpos($link, '_1920x1080.jpg');
    $downloan_image_name = substr($link, $name_start_pos, $name_end_pos - $name_start_pos);
    $image_path = $folder.'/'.$downloan_image_name.'.jpg';
    
    $api_obj = new stdClass();
    $cr = $copyright;
    $copyright_divider_pos = strpos($cr, '<br/>');;
    if ($copyright_divider_pos === false) {
        $copyright = $cr;
    } else {
        $copyright = substr($cr, 0, $copyright_divider_pos);
        $desc = substr($cr, $copyright_divider_pos + 5);
        $api_obj->desc = $desc;
    }

    $s_pos = strpos($copyright, '(');
    $title = substr($copyright, 0, $s_pos);
    $copyrightonly = substr($copyright, $s_pos + 1, -1);
    
    //$api_obj->title = $title;
    //$api_obj->caption = $title;
    $api_obj->copyright = $copyright;
    $api_obj->copyrightonly = $copyrightonly;
    $api_obj->date = $date.'';
    $api_obj->divider = 1;

    $prefix = 'OHR.';
    $name = $downloan_image_name;
    if ($prefix != substr($downloan_image_name, 0, 4)) {
        $name = $prefix.$downloan_image_name;
   }
    
    $image = new stdClass();
    $image->name = $name;
    
    $hd_url = 'tmp/'.$downloan_image_name.'.jpg'; 
    $hd_response = request($hd_url, 2);
    if ($hd_response === faslse) {
        $hd_url = 'tmp/'.$name.'.jpg';
        $hd_response = request($hd_url, 2);
    }
    if ($hd_response !== false) {
        $hash = strtoupper(hash('sha256', $hd_response));
        $hd_result = check_image_and_save('images/'.$hash.'.jpg', $hd_response);
        if ($hd_result !== false) {
            $image->hd = '/images/'.$hash.'.jpg';
        }
    }

    $hd_origin_url = 'https://www.bing.com/th?id='.$name.'_1920x1080.jpg&rf=LaDigue_1920x1080.jpg&pid=hp';
    //$hd_origin_resposne = request($hd_origin_url, 2);
    //if ($hd_origin_response !==false) {
    //    $image->binghd = $hd_origin_url;
    //}
    $image->binghd = $hd_origin_url;

    $tmb_url = 'tmb/'.$downloan_image_name.'_tmb.jpg';
    $tmb_response = request($tmb_url);
    if ($tmb_response === false) {
        $tmb_url = 'tmb/'.$name.'_tmb.jpg';
        $tmb_response = request($tmb_url);
    }
    if ($tmb_response !== false) {
        $hash = strtoupper(hash('sha256', $tmb_response));
        $tmb_result = check_image_and_save('images/'.$hash.'.jpg', $tmb_response);
        if ($tmb_result !== false) {
            $image->tmb = '/images/'.$hash.'.jpg';
        }
    }

    $tmb_origin_url = 'https://www.bing.com/th?id='.$name.'_tmb.jpg';
    //$tmb_origin_resposne = request($tmb_origin_url, 2);
    //if ($tmb_origin_response !==false) {
    //    $image->bingtmb = $tmb_origin_url;
    //}
    $image->bingtmb = $tmb_origin_url;
    
    $api_obj->image = $image;

    echo 'title = '.$api_obj->title.'
';
    echo 'caption = '.$api_obj->caption.'
';
    echo 'desc = '.$api_obj->desc.'
';
    echo 'copyright = '.$api_obj->copyright.'
';
    echo 'copyrightonly = '.$api_obj->copyrightonly.'
';
    echo 'date = '.$api_obj->date.'
';
    echo 'image->hd = '.$api_obj->image->hd.'
';
    echo 'image->tmb = '.$api_obj->image->tmb.'
';
    echo '-------------------
';
    $json = json_encode($api_obj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    check_path_and_save('./zh-CN/api/'.$year.'/'.$month.'/'.$year.'-'.$month.'-'.$day.'.json', $json);

    

//    if (!file_exists($image_path)) {
//        $download_cmd = 'wget -O '.$image_path.' "'.$link.'"';
//        exec($download_cmd);
//    }
//    echo $image_path.'
//';
//    echo $link.'
//';
//    echo $download_cmd.'
//';
//    echo '-------------------
//';
}
echo 'tmp count = '.$tmp_count.'
';
