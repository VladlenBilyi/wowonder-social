<?php
if (!isset($_GET['hash']) || empty($_GET['hash'])) {
	header("Location: " . $wo['config']['site_url']);
    exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = '';
$wo['page']        = 'hashtag';
$wo['title']       = '#' . $_GET['hash'] . ' | ' . $wo['config']['siteTitle'];
$wo['content'] = Wo_LoadPage('hashtags/content');