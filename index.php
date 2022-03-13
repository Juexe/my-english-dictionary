<!DOCTYPE html>
<html lang="en">
<head>
    <title>模糊查词 - 小糊涂查词</title>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="common/main.css?v=1.1">
</head>

<body>

<div id="content">
    <div id="input-area">
        <?php include 'common/nav.php'; ?>

        <label style="display: block">
            <input id="text" type="text" oninput="search()" placeholder="模糊查词">
        </label>
        <button id="text-clear" onclick="textClear()">×</button>
    </div>
    <div id="word-list">
        <div class="word-item">
            <span class="word-word" style="font-weight: bold">关于</span>
            <span class="word-translation"></span>
        </div>
        <div class="word-item">
            <span class="word-word">说明 |</span>
            <span class="word-translation" style="max-height: none">
                英英 / 中英搜索，词频排序，支持通配符模糊搜索。<br>
                百分号 <span class="word-key">%</span> 表示任意多个字母，下划线 <span class="word-key">_</span> 表示一个字母。
                <br><br>示例：
                <br>1：<span class="word-key">%</span>our 搜索 our 结尾的所有单词
                <br>2：coa<span class="word-key">%</span> 搜索 coa 开头的所有单词
                <br>3：p<span class="word-key">%</span>p 搜索 p 开头 p 结尾的所有单词
                <br>4：<span class="word-key">_</span>all 搜索 <span class="word-key">c</span>all、<span class="word-key">f</span>all、<span class="word-key">w</span>all 等词
                <br>5：p<span class="word-key">_</span>ll 搜索 p<span class="word-key">o</span>ll、p<span class="word-key">i</span>ll、p<span class="word-key">u</span>ll 等词
            </span>
        </div>
        <div class="word-item">
            <span class="word-word">词库 |</span>
            <span class="word-translation" style="max-height: none">词库出自柯林斯1~5级词频库， 收录14600词。</span>
        </div>
        <div class="word-item">
            <span class="word-word">词频 |</span>
            <span class="word-translation" style="max-height: none">如此条右上角， 词频由高到低5至1级排列。</span>
            <span class="word-collins collins-level-5">5</span>
        </div>
        <div class="word-item">
            <span class="word-word">版本 |</span>
            <span class="word-translation" style="max-height: none">
                <span style="color: black">v1.5 Beta</span> (2022-03-13)
                <br>重构，开源。
                <br>
                <span style="color: black">v1.4 Beta</span> (2018-08-13)
                <br>加入音标。
                <br>
                <span style="color: black">v1.3 Beta</span> (2018-08-02)
                <br>增加通配符搜索功能，下划线 <span class="word-key">_</span> 匹配一个字母，百分号 <span class="word-key">%</span> 匹配任意个字母
                <br>
                <span style="color: black">v1.2 Beta</span> (2018-07-31)
                <br>中文模糊搜索。词频等级上色。
                <br>
                <span style="color: black">v1.1 Beta</span> (2018-07-29)
                <br>搜索关键字高亮；修复：输入过快串词现象。
                <br>
                <span style="color: black">v1.0 Beta</span> (2018-07-22)
                <br>英文模糊搜索。
            </span>
        </div>
    </div>
</div>
<!-- 依赖库 -->
<script src="https://cdnjs.loli.net/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.loli.net/ajax/libs/jquery.loadtemplate/1.5.10/jquery.loadTemplate.min.js"></script>
<!-- 模版 -->
<script type="text/html" id="tmpl-demo">
    <div class="word-item" onclick="removeHeightLimit(this)">
        <span class="word-word" data-content="word" data-format="keyHighlightFormatter">word</span>
        <span class="word-translation" data-content="translation" data-format="newLineFormatter">中文释义</span>
        <span class="word-collins" data-content="collins" data-class="collins" data-format-target="class" data-format="levelClassFormatter">5</span>
    </div>
</script>
<!-- 主功能 -->
<script>
    $text = $('#text');
    $wordList = $('#word-list');

    var searchTimeout;
    function search() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            var text = $text.val();
            if (text===''){
                $('#text-clear').hide();
                return false;
            }
            $('#text-clear').show();
            $.get("mydict.php?text=" + text, function (res) {
                if (res === 'no') return false;
                $words = JSON.parse(res);
                $wordList.empty();
                $.each($words, function (i, word) {
                    if (word.phonetic==null) word.phonetic = '[=]\\n';
                    else word.phonetic = '['+word.phonetic+']\\n';

                    word.translation = word.phonetic + word.translation;
                    $wordList.loadTemplate($('#tmpl-demo'), word,{append:true});
                })
            });

        }, 300);
    }

    function textClear() {
        $text.val('');
        $('#text-clear').hide();
        $text.focus();
    }

    $.addTemplateFormatter({
        levelClassFormatter: function (value, template) {
            console.log(value);
            return 'collins-level-'+value;
        },
        newLineFormatter : function(value, template) {
            //return value.replace(/(\\r)*\\n/gi, '<br>');
            var text = $text.val();
            var value2 = value.replace(/(\\r)*\\n/gi, '<br>');
            var reg = eval('/(' + text + ')/gi');
            return value2.replace(reg, '<span class="word-key">$1</span>');
        },
        keyHighlightFormatter : function(value, template) {
            var text = $text.val();//用户输入

            // 如有字符 _

            var value2 = value;
            if (text.indexOf('_')!==-1){
                var text2 = text.replace('%','');
                var textArr = text2.split('_');
                $.each(textArr, function (i, n) {
                    if (n!==''){
                        var reg = eval('/(' + n + ')/gi');
                        value2 = value2.replace(reg, '【、$1【】');
                    }
                });
            }

            // 如有字符 %

            if (text.indexOf('%')!==-1){
                var text3 = text.replace('_','');
                var textArr2 = text3.split('%');
                $.each(textArr2, function (i, n) {
                    console.log(n);
                    if (n!==''){
                        var reg = eval('/(' + n + ')/gi');
                        value2 = value2.replace(reg, '【、$1【】');
                    }
                });
            }

            // 如未发现匹配符

            if (text.indexOf('_')===-1&&text.indexOf('%')===-1) {
                var reg = eval('/(' + text + ')/gi');
                return value.replace(reg, '<span class="word-key">$1</span>');
            }else{
                value2 = value2.replace(/【、/gi, '<span class="word-key">');
                value2 = value2.replace(/【】/gi, '</span>');
                return value2;
            }
        }
    });

    function removeHeightLimit(obj) {
        var trs = $(obj).children('.word-translation');
        //console.log(trs.css('max-height'));
        if(trs.height()>25){
            trs.css('max-height', '1.4rem')
        }else{
            trs.css('max-height', 'none')
        }
    }
</script>

</body>
</html>