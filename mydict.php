<?php
/**
 * Author: Juexe
 * Time: 18/07/19 18:12
 */
// 如果（没有 Referer 或者 Referer 非本地访问的）return 'error' 或 die() 程序结束
if(!isset($_SERVER['HTTP_REFERER']) || !strstr($_SERVER['HTTP_REFERER'], 'juexe.cn')){
    header('HTTP/1.1 403 Forbidden');
    echo "Access deny.";
    die();
}

require_once 'pdoc.php';
if (empty($_GET['text'])) exit('no');
$text = trim($_GET['text']);
if (empty($text)) exit('no');

// 搜索中文
if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $text)>0){
    $sql = "SELECT * FROM mydict WHERE `translation` LIKE ? ORDER BY word LIMIT 110";
}else{
    $sql = "SELECT * FROM mydict WHERE word LIKE ? ORDER BY word LIMIT 110";
}
// 通配符
if (strpos($text,'_')!==false || strpos($text,'%')!==false){
    $find = $text;
}else{
    $find = "%$text%";
}
$prep = $pdo->prepare($sql);
$prep->execute([$find]);
$res = $prep->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($res, JSON_UNESCAPED_UNICODE);


//todo 无限滚动 - 分页