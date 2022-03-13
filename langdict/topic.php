<?php
$topic = $_GET['topic'];
if (empty($topic)) exit('missing topic param');
?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>主题：<?=$topic?> - 朗文英语词典(Juexe Lab)</title>
    <style>
        body{
            max-width: 800px;
            margin: auto;
            padding: 2em;
        }
    </style>
</head>
<body>
<?php
$topic = str_replace('/', ' & ', $topic);
include "topic/" . $topic .".html";
?>
<script>
    // 链接修复
    let links = document.querySelectorAll('a[href]');
    links.onclick = function(){
        alert('1');
    };
    links.forEach(function (item) {
        let h = item.getAttribute('href');
        console.log(h);
        if (h.indexOf("entry://#") > -1){
            h = h.replace('entry://#', '#');
            console.log('rp', h);
        }else if(h.indexOf('entry://' > -1)){
            h = h.replace('entry://', '');
            let hs = h.split('#');
            h = '?topic=' + h;
            console.log(h);
        }
        item.setAttribute('href', h);
    });

    //tabindex
    let expons = document.querySelectorAll('.Exponent');
    expons.forEach(function (item) {
        item.setAttribute('tabindex', '0')
    });

    // stats
    var _mtac = {"senseQuery": 1};
    (function () {
        var mta = document.createElement("script");
        mta.src = "//pingjs.qq.com/h5/stats.js?v2.0.4";
        mta.setAttribute("name", "MTAH5");
        mta.setAttribute("sid", "500622493");
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(mta, s);
    })();
</script>
</body>
</html>