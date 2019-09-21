<?php
require_once('./language-list.php');

//更新Git账号信息为爬虫账号信息并同步更新仓库最新代码
exec('git config --local user.name "Sprider"');
exec('git config --local user.email "sprider@klavor"');
exec('git pull');

$language_list = get_language_list();
//遍历数组
foreach ($language_list as $language => $country) {
    //定义url相关信息
    $bing_domain   = 'https://global.bing.com';
    $bing_json_url = $bing_domain . '/HPImageArchive.aspx?setmkt=' . $language . '&setlang=' . $language . '&ensearch=0&format=js&idx=7&n=1&pid=hp&quiz=1&og=1&uhd=0';
    
    
    //拼接下载命令
    $json_temp_path = time() . '.json';
    
    $download_cmd = 'wget -O ' . $json_temp_path . ' "' . $bing_json_url . '"';
    echo 'Bing daily image api json downloan command: ' . $download_cmd . '
';
    //执行文件下载
    exec($download_cmd);
    
    
    //从本地读取下载好的api数据
    $json_temp_content = file_get_contents($json_temp_path);
    
    //转换成json对象
    $json_obj = json_decode($json_temp_content, false);
    
    //取出images数组，由于调用api时n=1,所以数字的length=1
    $images = $json_obj->images;
    
    //取出图片对象
    $image = $images[0];
    
    //取出变量
    $title         = $image->title;
    $caption       = $image->caption;
    $desc          = $image->desc;
    $copyrightonly = $image->copyrightonly;
    $copyright     = $image->copyright;
    $copyrightlink = $image->copyrightlink;
    $url           = $image->url;
    $urlbase       = $image->urlbase;
    $startdate     = $image->startdate;
    $fullstartdate = $image->fullstartdate;
    $enddate       = $image->enddate;
    $quiz          = $image->quiz;
    $hsh           = $image->hsh;
    $image_og      = $image->og;
    $og_title      = $image_og->title;
    $og_desc       = $image_og->desc;
    $og_img        = $image_og->img;
    $og_hash       = $image_og->hash;
    
    if (empty($title)) {
        $title = strstr($copyright, '(', true);
    }
    if (empty($caption)) {
        $caption = $title;
    }
    if (empty($copyrightonly)) {
        $copyrightonly = substr(strrchr($copyright, '('), 1, -1);
    }
    
    $request_year  = substr($startdate, 0, 4);
    $request_month = substr($startdate, 4, 2);
    $request_day   = substr($startdate, -2);
    echo 'request_year = ' . $request_year . '; request_month = ' . $request_month . '; request_day = ' . $request_day . '
';
    
    
    //移动json文件到对应目录下
    //定义存储数据的文件夹名称
    $bing_json_path = $language . '/' . 'json/' . $request_year . '/' . $request_month;
    //判断文件是否存在，如果不存在则创建目录
    if (!file_exists($bing_json_path)) {
        mkdir($bing_json_path, 777, true);
        echo 'Create json folder success!
';
    } else {
        echo 'Json folder exists！
';
    }
    
    //移动文件到json文件夹下存储
    $json_new_name     = $request_year . '-' . $request_month . '-' . $request_day . '.json';
    $json_move_command = 'mv ' . $json_temp_path . ' ' . $bing_json_path . '/' . $json_new_name;
    echo 'Json move command: ' . $json_move_command . '
';
    exec($json_move_command);
    
    
    //计算，用于判断是否提交git仓库还是还原操作
    $image_count = 0;
    
    
    //拼接域名获取到完整的图片地址
    $bing_image_url = $bing_domain . $url;
    //拼接图片存储文件夹路径
    $bing_image_dir = 'image';
    if (!file_exists($bing_image_dir)) {
        mkdir($bing_image_dir, 777, true);
        echo 'Create bing daily image folder success!
';
    } else {
        echo 'Bing daily image folder exists!
';
    }
    
    
    //生成图片文件名
    $bing_image_name = substr(strrchr($urlbase, '='), 1) . '.jpg';
    //拼接图片文件路径
    $bing_image_path = $bing_image_dir . '/' . $bing_image_name;
    //判断图片是否存在，不存在则下载图片
    if (!file_exists($bing_image_path)) {
        $image_count++;
        
        //拼接下载图片命令
        $bing_image_download_cmd = 'wget -O ' . $bing_image_path . ' "' . $bing_image_url . '"';
        echo 'Bing daily image downloan command: ' . $bing_image_download_cmd . '
';
        //执行图片下载命令
        exec($bing_image_download_cmd);
        echo 'Download bing daily image success!
';
    } else {
        echo 'Bing daily image exists!
';
    }
    
    //获取图片的sha256
    $bing_image_sha256      = hash_file('sha256', $bing_image_path);
    $bing_image_sha256_name = $bing_image_sha256 . '.jpg';
    $bing_image_sha256_path = $bing_image_dir . '/' . $bing_image_sha256_name;
    if (!file_exists($bing_image_sha256_path)) {
        //图片如果不存在则重命名
        rename($bing_image_path, $bing_image_sha256_path);
    } else {
        //文件已存在，将此次下载的图片删除
        unlink($bing_image_path);
    }
    
    //水印图片下载
    $og_image_name = substr(strrchr($og_img, '='), 1);
    $og_image_path = $bing_image_dir . '/' . $og_image_name;
    if (!file_exists($og_image_path)) {
        $image_count++;
        
        exec('wget -O ' . $og_image_path . ' "' . $og_img . '"');
        echo 'Downloa og image success!
';
    } else {
        echo 'Og image exists!
';
    }
    
    //获取图片的sha256
    $og_image_sha256      = hash_file('sha256', $og_image_path);
    $og_image_sha256_name = $og_image_sha256 . '.jpg';
    $og_image_sha256_path = $bing_image_dir . '/' . $og_image_sha256_name;
    if (!file_exists($og_image_sha256_path)) {
        //图片如果不存在则重命名
        rename($og_image_path, $og_image_sha256_path);
    } else {
        //文件已存在，将此次下载的图片删除
        unlink($og_image_path);
    }
    
    //拼接对外公布的接口数据
    $api_json                = new stdClass();
    $api_json->title         = $title;
    $api_json->caption       = $caption;
    $api_json->desc          = $desc;
    $api_json->copyright     = $copyright;
    $api_json->copyrightonly = $copyrightonly;
    $api_json->copyrightlink = $copyrightlink;
    $api_json->date          = $startdate;
    $api_json_image          = new stdClass();
    $api_json_image->name    = $bing_image_name;
    $api_json_image->hd      = '/' . $bing_image_sha256_path;
    $api_json_image->tmp     = '/' . $og_image_sha256_path;
    $api_json_image->binghd  = $bing_image_url;
    $api_json_image->bingtmb = $og_img;
    $api_json->image         = $api_json_image;
    
    //获取目录路径（如"./en/api"）
    $api_json_path = $language . '/api/' . $request_year . '/' . $request_month;
    //判断文件是否存在，如果不存在则创建目录
    if (!file_exists($api_json_path)) {
        mkdir($api_json_path, 777, true);
        echo 'Create api folder success!
';
    } else {
        echo 'Api folder exists!
';
    }
    
    //拼接api数据的文件路径
    $api_file_path = $api_json_path . '/' . $request_year . '-' . $request_month . '-' . $request_day . '.json';
    echo 'Api json path: ' . $api_file_path . '
';
    file_put_contents($api_file_path, json_encode($api_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    
    //判断是否继续后续流程。只有在有图片更新的情况下才继续后续流程。
    if ($image_count == 0) {
        //没有下载任何图片，则将仓库重置
        $reset_cmd = 'git add --all .;';
        $reset_cmd .= 'git reset --hard HEAD';
        exec($reset_cmd);
        continue;
    }
    
    
    //拼接git提交所需要的comment信息
    $comment = '【' . $language . '】' . $title . '
';
    $comment .= 'Caption: ' . $caption . '
';
    $comment .= 'Desc: ' . $desc . '
';
    $comment .= 'Copyrightonly: ' . $copyrightonly . '
';
    $comment .= 'Copyright: ' . $copyright . '
';
    $comment .= 'Copyrightlink: ' . $copyrightlink . '
';
    $comment .= 'Url: ' . $url . '
';
    $comment .= 'Urlbase: ' . $urlbase . '
';
    $comment .= 'Startdate: ' . $startdate . '
';
    $comment .= 'Fullstartdate: ' . $fullstartdate . '
';
    $comment .= 'Enddate: ' . $enddate . '
';
    $comment .= 'Quiz: ' . $quiz . '
';
    $comment .= 'Hsh: ' . $hsh . '
';
    $comment .= 'Og Title: ' . $og_title . '
';
    $comment .= 'Og Desc: ' . $og_desc . '
';
    $comment .= 'Og Img: ' . $og_img . '
';
    $comment .= 'Og Hash: ' . $og_hash . '
';
    
    //提交爬取数据
    exec('git add --all .');
    exec('git commit -m "' . $comment . '";');
    exec('git push');
}
//还原Git账号信息
exec('git config --local user.name "Klavor Lee"');
exec('git config --local user.email "lee@klavor.com"');
