<?php
$urlbase = 'http://bing.plmeizi.com/show/';
for ($i = 1; $i <= 1211; $i++) {
    exec('wget -O html/'.$i.'.html "'.$urlbase.$i.'"');
}
