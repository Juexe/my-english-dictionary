<?php
function nav_item($title, $path)
{
    $curr = ($path === parse_url($_SERVER['REQUEST_URI'])['path'] ?? false) ? 'current' : '';

    printf('<a href="%s" class="%s">%s</a>', $path, $curr, $title);
}

?>
<div id="menu-btn">
    <a href="/">πζ΄ε€</a>
</div>
<div id="nav">
    <?php
    nav_item('ζ₯θ―', '/dict/');
    nav_item('δΎε₯', '/dict/langdict/');
    nav_item('εηΌ', '/dict/word-root/');
    nav_item('εηΌ', '/dict/word-affix/');
    ?>
</div>