<?php
if (Wo_IsLogged() === false) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
    exit();
}
if (empty($_GET['page'])) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
    exit();
}
$wo['setting']['admin'] = false;
if (isset($_GET['page']) && !empty($_GET['page'])) {
    if (Wo_PageExists($_GET['page']) === false) {
        header("Location: " . Wo_SeoLink('index.php?tab1=404'));
        exit();
    }
    $page_id  = Wo_PageIdFromPagename($_GET['page']);
    $wo['setting']['admin'] = true;
    if (empty($page_id)) {
	    header("Location: " . $wo['config']['site_url']);
        exit();
    }
    $wo['setting'] = Wo_PageData($page_id);
}

if (Wo_IsPageOnwer($page_id) === false) {
	if (Wo_IsAdmin($wo['user']['user_id']) === false) {
		header("Location: " . $wo['config']['site_url']);
        exit();
	}
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'page_setting';
$wo['title']       = 'Page Setting | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('page-setting/content');