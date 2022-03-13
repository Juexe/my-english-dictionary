<?php
require_once '../pdoc.php';
$res = [];
if (!empty($_GET['text'])) {
    $text = trim($_GET['text']);
    if (!empty($text)) {
        $sql  = "SELECT f.word, LENGTH(f.word) AS word_len, f.translation AS trans, f.phonetic AS ph , f.collins AS col, f.w2 AS word2, f.t2 AS trans2, f.p2 AS ph2, f.c2 AS col2 FROM ( SELECT * FROM mydict froms JOIN ( SELECT word AS w2, translation AS t2, phonetic AS p2, collins AS c2 , LEFT(word, LENGTH(word) - ?) AS word_l FROM mydict WHERE word LIKE ? AND phonetic IS NOT NULL ) tos WHERE BINARY froms.word = BINARY tos.word_l AND froms.phonetic IS NOT NULL ) f ORDER BY word_len DESC;";
        $find = "%$text";
        $prep = $pdo->prepare($sql);
        $prep->execute([strlen($text), $find]);
        $res = $prep->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>后缀关联词查询 - 小糊涂查词</title>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="/dict/common/main.css?v=1.1">
</head>
<style>
    #content {
        max-width: 800px;
    }

    /*
     * 单词列表
     */

    #word-list {
        flex-grow: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .word-item {
        padding: 12px 1rem;
        border-bottom: 10px solid #eaeaea;
        position: relative;
        display: flex;
    }

    .item-left, .item-right {
        width: 50%;
        position: relative;
    }

    .item-right {
        padding-left: 15px;
        border-left: 1px solid #eaeaea;
    }

</style>
<body>
<div id="content">
    <div id="input-area">
        <?php include '../common/nav.php'; ?>

        <label>
            <form onsubmit="return search();">
                <input id="text" type="text" placeholder="后缀搜索：ist、lly..." value="<?= @$_GET['text'] ?>">
            </form>
        </label>
        <div style="margin-top: 9px; color: #474747">
            <?php if (!empty($text)): ?>
                搜索结果：
                <b><?= count($res) ?></b> 组可能存在关系的单词。
            <?php else: ?>
                按回车搜索。
            <?php endif; ?>
        </div>
    </div>
    <script>
        function search() {
            let text2 = document.getElementById('text').value;
            this.location.href = '/dict/word-affix/?text=' + text2;
            return false;
        }
    </script>
    <?php if (!empty($res)): ?>
        <div id="word-list">
            <div class="word-item" style="font-weight: bold">
                <div class="item-left">
                    <span class="word-word">带该后缀 👇</span>
                </div>

                <div class="item-right">
                    <span class="word-word">去掉后缀 👇</span>
                </div>
            </div>
            <?php foreach ($res as $it):
                ?>
                <div class="word-item">
                    <div class="item-left">
                        <span class="word-word">
                            <?= str_replace($it['word'], '<span class="word-key">' . $it['word'] . '</span>', $it['word2']) ?>
                        </span>
                        <span class="word-translation">[<?= $it['ph2'] ?>]
                            <br><?= str_replace('\n', '<br>', $it['trans2']) ?></span>
                        <!--                    <span class="word-collins collins-level---><? //= $it['col2']
                        ?><!--">--><? //= $it['col2']
                        ?><!--</span>-->
                    </div>
                    <div class="item-right">
                        <span class="word-word"><?= $it['word'] ?></span>
                        <span class="word-translation">[<?= $it['ph'] ?>]
                            <br> <?= str_replace('\n', '<br>', $it['trans']) ?></span>
                        <!--                    <span class="word-collins collins-level---><? //= $it['col']
                        ?><!--">--><? //= $it['col']
                        ?><!--</span>-->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script>
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