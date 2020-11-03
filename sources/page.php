<?php
if (isset($_GET['p'])) {
    if (Wo_PageExists($_GET['p']) === true && Wo_PageActive($_GET['p'])) {
        $page_id            = Wo_PageIdFromPagename($_GET['p']);
        $wo['page_profile'] = Wo_PageData($page_id);
    } else {
        header("Location: " . Wo_SeoLink('index.php?tab1=404'));
        exit();
    }
} else {
    header("Location: " . $wo['config']['site_url']);
    exit();
}

$wo['description'] = $wo['page_profile']['page_description'];
$wo['keywords']    = '';
$wo['page']        = 'page';
$wo['title']       = $wo['page_profile']['name'];
$wo['content']     = Wo_LoadPage('page/content');