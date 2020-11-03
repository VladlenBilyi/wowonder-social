<?php
session_destroy();
if (isset($_COOKIE['user_id'])) {
     setcookie('user_id', '', time()-300);
}
header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
exit();