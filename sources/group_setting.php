<?php
if (Wo_IsLogged() === false) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
    exit();
}
if (empty($_GET['group'])) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
    exit();
}
$wo['setting']['admin'] = false;
if (isset($_GET['group']) && !empty($_GET['group'])) {
    if (Wo_GroupExists($_GET['group']) === false) {
        header("Location: " . Wo_SeoLink('index.php?tab1=404'));
        exit();
    }
    $group_id  = Wo_GroupIdFromGroupname($_GET['group']);
    $wo['setting']['admin'] = true;
    if (empty($group_id)) {
	    header("Location: " . $wo['config']['site_url']);
        exit();
    }
    $wo['setting'] = Wo_GroupData($group_id);
}

if (Wo_IsGroupOnwer($group_id) === false) {
	if (Wo_IsAdmin($wo['user']['user_id']) === false) {
		header("Location: " . $wo['config']['site_url']);
        exit();
	}
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'group_setting';
$wo['title']       = 'Group Setting | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('group-setting/content');