<?php
$isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT']);
if ($isMobile) {
    header('Location: https://m.shibilgamer.online');
    exit;
}
?>
