<?php
function nav_item($title, $path)
{
    $curr = ($path === parse_url($_SERVER['REQUEST_URI'])['path'] ?? false) ? 'current' : '';

    printf('<a href="%s" class="%s">%s</a>', $path, $curr, $title);
}

?>
<div id="menu-btn">
    <a href="/">🔗更多</a>
</div>
<div id="nav">
    <?php
    nav_item('查词', '/dict/');
    nav_item('例句', '/dict/langdict/');
    nav_item('前缀', '/dict/word-root/');
    nav_item('后缀', '/dict/word-affix/');
    ?>
</div>