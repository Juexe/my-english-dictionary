<?php
require_once '../pdoc.php';
$pre_title = '';
$wordTree  = init();
function init()
{
    global $pre_title;
    if (!empty($_GET['text'])) {
        $res  = [];
        $text = trim($_GET['text']);
        if (!empty($text)) {
            $sql       = "SELECT * FROM topics WHERE word like ? LIMIT 30";
            $find      = "%$text%";
            $res       = db_query($sql, [$find]);
            $pre_title = $text . ' - ';
        }
        return word_data_to_array($res);
    }
    return [];
}

function get_sentences($topic, $subtopic, $word)
{
    $sql = "SELECT * FROM sentences WHERE topic=? AND subtopic=? AND word=? LIMIT 5";
    $res = db_query($sql, [$topic, $subtopic, $word]);
    return $res;
}

function db_query($sql, $params)
{
    global $pdo;
    $prep = $pdo->prepare($sql);
    $prep->execute($params);
    return $prep->fetchAll(PDO::FETCH_ASSOC);
}

function word_data_to_array($res)
{
    $tree = [];
    foreach ($res as $w) {
        $top = $w['topic'];
        //$sub = $w['subtopic'];
        $word = $w['word'];
        if (empty($tree[$top][$word]))
            $tree[$top][$word] = [];
        array_push($tree[$top][$word], $w);
    }
    return $tree;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $pre_title ?>朗文英语 - 小糊涂查词</title>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="/dict/common/main.css?v=1.1">
    <style>
        #word-list * {
            font-size: medium;
        }

        .word-topic {
            text-align: center;
            background: #005cab;
            color: white;
            font-weight: bold;
            padding: 3px;
        }

        .word-topic a {
            color: white;
        }

        .word-topic a:hover {
            color: #ff1200;
        }

        .word-subtopic {
            color: #56b13f;
            font-weight: bold;
            margin: 8px 0;
            /*display: inline-block;*/
            /*color: white;*/
            /*padding: 0 8px;*/
            /*background-color: #4c6ebc;*/
            /*border-radius: 20px;*/
            /*border-width: 1px;*/
        }

        .word-word {
            border-bottom: 3px solid red;
            font-weight: bold;
        }

        .word-sentence, .word-sentence-zh {
            font-size: small;
            display: flex;
        }

        .word-sentence {
            color: darkblue;
        }

        .word-sentence:before {
            content: '[例]';
            color: lightgray;
            padding-right: 4px;
        }

        .word-sentence-zh {
            visibility: hidden;
            color: #5B5B5B;
        }

        .word-sentence-zh:before {
            content: '[译]';
            color: lightgray;
            padding-right: 4px;
        }

        .word-sentence-group {
            margin: 10px 0;
        }

        .word-sentence-group:hover .word-sentence-zh {
            visibility: visible;
        }

        #text {
            border-color: dodgerblue;
        }
    </style>
</head>
<body>
<div id="content">
    <div id="input-area">
        <?php include '../common/nav.php'; ?>

        <label>
            <form onsubmit="return search();">
                <input id="text" type="text" placeholder="朗文例句" value="<?= @$_GET['text'] ?>">
            </form>
        </label>
        <div id="search-tips" style="margin-top: 9px; color: #474747">
            <?php if (!empty($_GET['text'])): ?>
                搜索结果：
                <b><?= count($wordTree) ?></b> 个主题。
            <?php else: ?>
                按回车搜索。
            <?php endif; ?>
        </div>
    </div>
    <script>
        function search() {
            let text2 = document.getElementById('text').value;
            this.location.href = '/dict/langdict/?text=' + text2;
            return false;
        }
    </script>

    <div id="word-list">
        <?php foreach ($wordTree as $topic => $words): ?>
            <div class="word-topic">
                <a title="查看主题" href="topic.php?topic=<?= $topic ?>" target="_blank">💡&nbsp;<?= $topic ?>&nbsp;</a>
            </div>
            <?php foreach ($words as $word): ?>
                <?php $w = $word[0]; ?>
                <div class="word-item">
                    <div>
                        <span class="word-word"><?= $w['word'] ?></span>
                        <span class="word-translation">[<?= $w['pron'] ?>] &nbsp;/&nbsp;(<?= $w['type'] ?>)</span>
                    </div>
                    <?php foreach ($word as $m): ?>
                        <div>
                            <div class="word-subtopic"><?= $m['subtopic_zh'] ?></div>
                            <?php foreach (get_sentences($topic, $m['subtopic'], $m['word']) as $s): ?>
                                <div class="word-sentence-group">
                                <span class="word-sentence">
                                    <?= $s['sentence'] ?>
                                </span>
                                    <span class="word-sentence-zh">
                                    <?= $s['sentence_zh'] ?>
                                </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php if (!empty($_GET['text'])): ?>
            搜索结果：<b><?= count($wordTree) ?></b> 个主题。
        <?php else: ?>
            <div class="word-topic">
                <span style="color: white">使用说明</>
            </div>
            <div class="word-item">
                <span class="word-word">数据收录</span>
                <span class="word-translation">收录《朗文活用英语词典》：2万余单词和近6万例句。</span>
            </div>
            <div class="word-item">
                <span class="word-word">Anki 制卡</span>
                <span class="word-translation">支持PC端快速制卡，即点即制卡。</span>
            </div>
            <div class="word-item">
                <span class="word-word">模糊搜索</span>
                <span class="word-translation">支持单词模糊搜索。</span>
            </div>
            <div class="word-item">
            </div>

            <div class="word-topic">
                <span style="color: white">更新记录</>
            </div>
            <div class="word-item">
                <div>
                    <span class="word-word">v0.3 beta</span>
                    <span class="word-translation">2020/04/04</span>
                </div>
                <div>
                    <div class="word">新增 AnkiConnect 功能；</div>
                    <div class="word">支持 PC 端 Anki 快速单词制卡（beta 需要配置）。</div>
                </div>
            </div>
            <div class="word-item">
                <div>
                    <span class="word-word">v0.2 beta</span>
                    <span class="word-translation">2020/04/01</span>
                </div>
                <div>
                    <div class="word">新增单词主题浏览页面。</div>
                </div>
            </div>
            <div class="word-item">
                <div>
                    <span class="word-word">v0.1 beta</span>
                    <span class="word-translation">2020/03/31</span>
                </div>
                <div>
                    <div class="word">初始版本。</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
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

    // 2020/04/04
    // anki_connect：支持anki快速录入

    let ANKI_CONNECT = 'http://localhost:8765';
    let deckName = '英语生词';
    let modelName = '问答题';
    let apiKey = 'juexe';
    let autoClose = true;


    initAnki();

    // Anki 连接初始化
    function initAnki() {
        ankiGo({
            "action": "deckNames",
            "version": 6
        }).then(function (data) {
            let erro = data['error'];
            console.log(erro);
            if (erro != null) {
                alert('连接 AnkiConnect 失败：' + erro);
                console.log(erro);
            } else {
                console.log('连接AnkiConnect成功');
                initOnclickListener();
                showAnkiTips();
            }
        }).catch(function (err) {
            console.warn('连接AnkiConnect失败', err);
        })
    }

    // 显示连接提示
    function showAnkiTips() {
        let div = document.querySelector('#version-text');
        div.textContent += '(Anki已连接)'
    }

    // 添加卡片
    function addCard(front, backend, chapter = '朗文活用英语') {
        ankiGo({
            "action": "guiAddCards",
            "version": 6,
            "params": {
                "note": {
                    "deckName": deckName,
                    "modelName": modelName,
                    "fields": {
                        "正面": front,
                        "背面": backend,
                        "章节": chapter
                    },
                    "options": {
                        "closeAfterAdding": autoClose
                    },
                    "tags": []
                }
            }
        }).then(function (data) {
        }).catch(function (err) {
            alert('连接 AnkiConnect 失败');
            console.log(err);
        })
    }

    // 初始化点击事件
    function initOnclickListener() {
        let groups = document.querySelectorAll('.word-sentence-group');
        groups.forEach(function (group) {
            group.onclick = function () {
                let frontText = this.querySelector('.word-sentence').innerText;
                let backText = this.querySelector('.word-sentence-zh').innerText;
                let selText = window.getSelection().toString();
                frontText = frontText.replace(selText, '<b>' + selText + '</b>');
                addCard(frontText, backText);
                //console.log(frontText, backText)
            }
        })
    }

    // Connect 请求
    function ankiGo(req) {
        req['key'] = apiKey;
        return new Promise((resolve, reject) => fetch(ANKI_CONNECT, {
                method: 'POST',
                headers: {
                    'Content-type': 'text/json'
                },
                mode: 'cors',
                body: JSON.stringify(req),
            })
                .then(res => res.json())
                .then(data => resolve(data))
                .catch(err => reject(err))
        )
    }
</script>
</body>
</html>