<?php
require_once('./lib/function.php');

$urlbase = 'http://bing.plmeizi.com/show/';
$logcat = '';
for ($i = 1; $i <= 1200; $i++) {
    $response = request($urlbase.$i, 2);
    echo $urlbase.$i.'
';
    if ($response === false) {
        $logcat .= $i.'
';
        continue;
    }
    check_path_and_save('html/'.$i.'.html', $response);
}
echo $locat.'
';
