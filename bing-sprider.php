<?php
//根据测试发现必应每日一图api中会根据请求头中的cookie来判断是返回国内版还是国际版每日一图
$ensearchs = array(
    'cn' => 0, //国内版
    'en' => 1 //国际版
);
//遍历数组
foreach ($ensearchs as $ensearch_key => $ensearch_value) {
    //更新Git账号信息为爬虫账号信息并同步更新仓库最新代码
    exec('git config --local user.name "Sprider"');
    exec('git config --local user.email "sprider@klavor"');
    exec('git pull');
    
    //定义存储数据的文件夹名称
    $bing_json_dir  = 'json/' . date('Y') . '/' . date('m');
    //获取目录路径（如"./en/json"）
    $bing_json_path = $ensearch_key . '/' . $bing_json_dir;
    //判断文件是否存在，如果不存在则创建目录
    if (!file_exists($bing_json_path)) {
        mkdir($bing_json_path, 777, true);
        echo 'json文件夹不存在,已创建成功！
';
    }
    //拼接api数据的文件路径
    $file_path = $bing_json_path . '/' . date('Y-m-d') . '.json';
    echo '文件名称:' . $file_path . '
';
    $bing_json_url = '"https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&pid=hp&ensearch=' . $ensearch_value . '&quiz=1&og=1&uhd=0"';
    //请求网络获取api数据
    echo '正在下载今日bing数据...
';
    //拼接下载命令
    $download_cmd = 'wget -O ' . $file_path . ' ' . $bing_json_url;
    echo $download_cmd . '
';
    //执行文件下载
    exec($download_cmd);
    //不知道为什么用下面的方法会报异常
    ////PHP Warning:
    ////		file_get_contents("https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1"):
    ////		failed to open stream: No such file or directory in /root/photo-collections/bing-sprider.php on line 36
    ////Warning: 
    ////      file_get_contents("https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1"):
    ////		failed to open stream: No such file or directory in /root/photo-collections/bing-sprider.php on line 36
    //$request_array = array(
    //    'http' => array(
    //       'header' => 'cookie:ENSEARCH=BENVER=' . $ensearch_value,
    //        'authority' => 'www.bing.com',
    //        'method' => 'GET',
    //        'path' => '/HPImageArchive.aspx?format=js&idx=0&n=1',
    //        'scheme' => 'http'
    //    )
    //);
    //var_dump($request_array);
    //$request_context = stream_context_create($request_array);
    //$json_content = file_get_contents($bing_json_url, false, $request_context);
    //$json_content = fopen($bing_json_url, 'r', false, $request_context);
    echo '今日bing数据下载成功！
';
    
    //file_put_contents($file_path, $json_content);
    //从本地读取下载好的api数据
    $file_open    = fopen($file_path, 'r');
    $json_content = fread($file_open, filesize($file_path));
    //转换成json对象
    //$json_obj = json_decode($json_content, true);
    $json_obj     = json_decode($json_content, false);
    
    //var_dump($json_obj);
    //var_dump($json_obj->images);
    //取出images数组，由于调用api时n=1,所以数字的length=1
    $images = $json_obj->images;
    
    //取出图片对象
    $image = $images[0];
    //var_dump($image);
    
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
    
    $file_name = substr(strrchr($urlbase, '='), 1);
    
    $image_count = 0;
    
    //修改json文件名称
    //exec('mv ' . $file_path . ' ' . $bing_json_path . '/' . $file_name . '.json');
    
    //$json_content = file_get_contents($bing_json_url);
    //$json_obj = json_decode($json_content, true);
    $bing_site      = 'https://www.bing.com';
    //拼接域名获取到完整的图片地址
    $bing_image_url = $bing_site . $url;
    $sub_image_dir  = substr($startdate, 0, -2);
    $sub_image_dir  = substr_replace($sub_image_dir, '/', 4, 0);
    //拼接图片存储文件夹路径
    $image_dir      = $ensearch_key . '/' . $sub_image_dir;
    if (!file_exists($image_dir)) {
        mkdir($image_dir, 777, true);
    }
    //生成图片文件名
    $image_name = $file_name . '.jpg';
    //拼接图片文件路径
    $image_path = $image_dir . '/' . $image_name;
    //判断图片是否存在，不存在则下载图片
    if (!file_exists($image_path)) {
        $image_count++;
        //拼接下载图片命令
        $image_download_cmd = 'wget -O ' . $image_path . ' "' . $bing_image_url . '"';
        echo $image_download_cmd . '
';
        //执行图片下载命令
        exec($image_download_cmd);
        echo 'bing每日一图下载完成！
';
    } else {
        echo '图片已存在！
';
    }
    
    //水印图片下载
    $og_image_name = substr(strrchr($og_img, '='), 1);
    $og_image_path = $image_dir . '/' . $og_image_name;
    if (!file_exists($og_image_path)) {
        $image_count++;
        exec('wget -O ' . $og_image_path . ' "' . $og_img . '"');
        echo 'bing水印图片下载完成！
';
    } else {
        echo 'bing水印图片已存在！
';
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
    $api_json_image->hd      = $image_path;
    $api_json_image->tmp     = $og_image_path;
    $api_json_image->oghd    = $bing_image_url;
    $api_json_image->ogtmp   = $og_img;
    $api_json->image         = $api_json_image;
    
    //定义存储api数据的文件夹名称
    $api_json_dir  = 'api/' . date('Y') . '/' . date('m');
    //获取目录路径（如"./en/api"）
    $api_json_path = $ensearch_key . '/' . $api_json_dir;
    //判断文件是否存在，如果不存在则创建目录
    if (!file_exists($api_json_path)) {
        mkdir($api_json_path, 777, true);
        echo 'api文件夹不存在,已创建成功！
';
    }
    //拼接api数据的文件路径
    $api_file_path = $api_json_path . '/' . date('Y-m-d') . '.json';
    echo '文件名称:' . $api_file_path . '
';
    file_put_contents($api_file_path, urldecode(json_encode($api_json)));
    
    if ($image_count == 0) {
        //没有下载任何图片，则将仓库重置
        $reset_cmd = 'git add --all .;';
        $reset_cmd .= 'git reset --hard HEAD';
        exec($reset_cmd);
        continue;
    }
    
    //拼接git提交所需要的comment信息
    $comment = 'Title: ' . $title . '
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
    
    //拼接git提交的shell脚本
    //$git_cmd = 'git config --local user.name "Sprider";';
    //$git_cmd .= 'git config --local user.email "sprider@klavor";';
    //$git_cmd .= 'git pull;';
    //$git_cmd .= 'git add --all .;';
    //$git_cmd .= 'git commit -m "' . $comment . '";';
    //$git_cmd .= 'git push;';
    //$git_cmd .= 'git config --local user.name "Klavor Lee";';
    //$git_cmd .= 'git config --local user.email "lee@klavor.com";';
    
    //将shell脚本输出到本地，需要注意的是需要可执行权限。否则不能执行shell脚本，从而导致提交代码失败。
    //$shell_path = 'git.sh';
    //file_put_contents($shell_path, $git_cmd);
    //chmod($shell_path, 777);
    //执行shell脚本
    //exec('./git.sh');
    
    //下面的方式不能够正常运行，因此替换成了shell脚本的方式
    ////报错信息：
    ////Vim：Warning：Output is not to a terminal
    //提交爬取数据
    exec('git add --all .');
    exec('git commit -m "' . $comment . '";');
    exec('git push');
    //还原Git账号信息
    exec('git config --local user.name "Klavor Lee"');
    exec('git config --local user.email "lee@klavor.com"');
}