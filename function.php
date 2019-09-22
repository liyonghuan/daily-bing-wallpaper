<?php
function file_get_contents_retry($url, $time = 5) {
	$i = 0;
	do {
		echo 'retry time = '.$i.'; request url = '.$url.'
';
		$content = file_get_contents($url);
		if ($content !== false) {
			return $content;
		}
		$i++;
	} while ($i < $time);
	return false;
}

function reset_git() {
	//没有下载任何图片，则将仓库重置
	$reset_cmd = 'git add --all .;';
	$reset_cmd .= 'git reset --hard HEAD';
	exec($reset_cmd);
}
