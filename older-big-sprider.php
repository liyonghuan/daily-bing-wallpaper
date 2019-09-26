<?php
require_once('./lib/languages.php');
require_once('./lib/function.php');

define('ONE_DAY', 24 * 60 * 60);

$langs = get_languages();
foreach($langs as $lang_region => $region) {
    $time = time();
    while ($time) {
        $dateymd = date('Y-m-d', $time);
        $json_path = $lang_region.'/api/'.date('Y/m/Y-m-d', $time).'.json';      
        $response = request($json_path, 1);
        if ($resposne === false) {
            $time -= ONE_DAY;
            if ($time < time() - 1000 * ONE_DAY) {
		break;
	    }
            continue;
        }
        
        $obj = json_decode($response, false);
        $bing_url = $obj->image->binghd;
        $time -= ONE_DAY;
        if ($bing_url == "" || !isset($bing_url)) {
            echo $bing_url.'
';
            if ($time < time() - ONE_DAY * 1000) {
break;
}
            continue;
        }
        $bing_url = str_replace('1920x1080', 'UHD', $bing_url);
        echo $bing_url.'
';      
	$bing_response = request($bing_url);
	if ($bing_response !== false) {
	    $hash = hash('sha256', $bing_response);
            $result = check_image_and_save('images/'.$hash.'.jpg', $bing_response);
        if ($result === false) {
	echo '失败！
';
}        
    $j = new stdClass();
		$j->name = $obj->image->name;
 $j->hash = $hash;
$jj = json_encode($j);
check_path_and_save('json/'.$lang_region.'-'.$dateymd.'.json', $jj);
	}

        if ($time < time() - ONE_DAY * 1000) {
            break;
        } 
    }
}

