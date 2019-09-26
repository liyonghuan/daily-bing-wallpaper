### 简介（intro）

每日必应壁纸具备的功能有必应壁纸数据采集、必应壁纸数据存储、必应壁纸数据展示和必应壁纸数据API。你可以轻松的获取到每日必应壁纸，从此不留遗憾。

每日必应壁纸历史数据来源于[bing.plmeizi.com](http://bing.plmeizi.com)。

### 分支

#### master

master分支主要用于每日必应壁纸的采集、必应壁纸的浏览以及API访问的功能。

#### spider

spider分支主要用于每日必应壁纸采集脚本的发布，此分支仅包含稳定版的采集脚本相关的代码。

#### older-big-images

older-big-images分支存储的是每日必应壁纸的历史大图数据，即图片最大尺寸的高清图片，每张图片有好几M的那种，另外尺寸不一，属于原图类型。

#### big-images

big-images分支同older-big-images分支功能一样，但数据是从2019年09月26日开始记录。

#### bing-plmeizi-com

bing-plmeizi-com分支主要是保存了从bing.plmeizi.com采集数据脚本代码和相关的采集数据。后期已无其他用途。

### 必应API

- 必应API

```
https://cn.bing.com/HPImageArchive.aspx?format=hp&idx=0&n=1&pid=hp&ensearch=1&quiz=1&og=1&uhd=1&uhdwidth=2880&uhdheight=1620&setmkt=en-us&setlang=en-us
```

- [参数说明](https://www.klavor.com/dev/20190920-664.html)

param|description
:-|:-
format|返回的数据格式。hp为html格式；js为json格式；其他值为xml格式。
idx|获取特定时间点的数据。如idx=1表示前一天（昨天），依此类推。经过测试最大值为7。
n|获取数据的条数。经测试，配合上idx最大可以获取到13天前的数据，即idx=7&n=7。
pid|未知。pid为hp时，copyrightlink返回的是相对地址。pid不为hp时，没有看到og信息。
ensearch|指定获取必应【国际版/国内版】的每日一图。当ensearch=1时，获取到的是必应国际版的每日一图数据。默认情况和其他值情况下，获取到的是必应国内版的每日一图数据。
quiz|当quiz=1时，返回必应小测验所需的相关数据。
og|水印图相关的信息。包含了title、img、desc和hash等信息。
uhd|当uhd=1时，可以自定义图片的宽高。当uhd=0时，返回的是固定宽高（1920x1080）的图片数据。
uhdwidth|图片宽度。当uhd=1时生效。最大值为3840，超过这个值当作3840处理。
uhdheight|图片高度。当uhd=1时生效。最大值为2592，超过这个值当作2592处理。
setmkt|指定图片相关的区域信息。如图片名中包含的EN-CN、EN-US或者ZH-CN等。当域名为global.bing.com时才会有相应变化。值的格式：en-us、zh-cn等。
setlang|指定返回数据所使用的语言。值的格式：en-us、zh-cn等。

上述不一定详细而准确，但是差不多这样子的。

### 脚本使用

从[release](https://github.com/facefruit/daily-bing-wallpaper/releases)中下载最新的脚本代码到本地或服务器。
解压后得到spider文件，直接通过```php spider```命令运行php采集脚本并等待采集完成即可。

### 壁纸浏览

通过浏览器打开[https://daily-bing-wallpaper.devlang.org](https://daily-bing-wallpaper.devlang.org)即可进行访问。
另外，可以设置不同的参数来选择浏览不同国家地区、时间点和图片类型的必应壁纸。

param|description
:-|:-
region|国家地区语言编码，如zh-CN，区分大小写，语言zh为小写，国家CN为大写。
date|选择浏览的必应壁纸时间戳。单位为毫秒。
type|图片类型。hd：本地图片（1920x1080）；tmb：本地带水印图片；binghd：必应图片链接直接访问；bingtmb：必应带水印图片链接直接访问。

其中type为hd或者tmb访问的时候会比较慢，但是数据会比binghd和bingtmb多。大约在2018年06月之前是不支持binghd和bingtmb的，如果是要访问早于这个时间的必应壁纸，建议将type设置为hd。