<?php
if (Wo_IsLogged() === false) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
    exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'setting';
$wo['title']       = $wo['lang']['setting'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('setting/content');