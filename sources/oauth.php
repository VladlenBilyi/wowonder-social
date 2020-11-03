<?php

/* API / Developers (will be available on future updates) */

die();

if (empty($_GET['app_id'])) {
	
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));

	exit();

}

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if (Wo_IsLogged() === false) {

  header("Location: " . Wo_SeoLink('index.php?tab1=welcome') . '?last_url=' . urlencode($actual_link));

  exit();

}

$wo['app'] = array();

$app = Wo_IsValidApp($_GET['app_id']);

if ($app === true) {

	$app_id = Wo_GetIdFromAppID($_GET['app_id']);
	
	$wo['app'] = Wo_GetApp($app_id);
    
    if (Wo_AppHasPermission($wo['user']['user_id'], Wo_GetIdFromAppID($_GET['app_id'])) === true) {
    	
    	$url = $wo['app']['app_website_url'];

    	if (!empty($_GET['redirect_uri']) || empty($url)) {
    		
    		$url = $_GET['redirect_uri'];

    	}

    	header("Location: {$url}");

    }

}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'graph';
$wo['title']       = $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('graph/data-request');