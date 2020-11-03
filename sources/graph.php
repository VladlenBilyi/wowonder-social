<?php

/* API / Developers (will be available on future updates) */

die();

if (empty($_GET['app_id']) && empty($_GET['app_secret'])) {
	
	die();

}

if (Wo_AccessToken($_GET['app_id'], $_GET['app_secret']) === false) {
	
	die();

}

if (empty($_GET['access_token']) || !isset($_GET['access_token'])) {
	
	$user_id = 2;

	$token = Wo_GenrateToken($user_id, Wo_GetIdFromAppID($_GET['app_id']));

    if ($token !== false) {

       $url_data = array('status' => Wo_SeoLink('index.php?tab1=graph-success?access_token=' . $token));

       header("Content-type: application/json");

       echo json_encode($url_data, JSON_PRETTY_PRINT);

       exit();

    }

}

?>