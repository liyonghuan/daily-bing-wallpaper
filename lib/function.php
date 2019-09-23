<?php
/**
 * 网络请求
 */
function request($url, $time = 5) {
    if (empty($url)) {
        //日志记录
        return false;
    }
    $i = 0;
    do {
        $content = file_get_contents($url);
        if ($content !== false) {
            return $content;
        }
        echo 'Request failure! Time = '.$i.'; Url = '.$url.'
';
        $i++;
    } while ($i < $time);
    return false;
}

/**
 * 检测文件所在路径文件夹是否存在，
 * 不存在则创建文件夹并保存数据。
 */
function check_path_and_save($path, $content) {
    //获取目录
    $dir = substr($path, 0, strrpos($path, '/'));
    if (!file_exists($dir)) {
        mkdir($dir, 755, true);
    }
    return file_put_contents($path, $content);
} 


/**
 * 检测图片是否已存在，
 * 如果不存在则保存图片，
 * 如果存在且为同一个文件则保存图片，
 * 如果存在且不为同一个文件则保存为备份原文件。
 */
function check_image_and_save($path, $content) {
    if (file_exists($path)) {
        //图片已存在
        if (filesize($path) != strlen($content)) {
            //视为不同图片
            $bak_path = $path.'.'.strtoupper(uniqid()).'.bak';
            return file_put_contents($bak_path, $content);
        }
        return true;
    } else {
        //图片不存在
        return check_path_and_save($path, $content);
    }
}
