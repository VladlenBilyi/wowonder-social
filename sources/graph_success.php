<?php

die();

header("Content-type: application/json");

if (empty($_GET['access_token'])) {
	
	exit();

}

$user_id = Wo_UserIdFromToken($_GET['access_token']);

if ($user_id === false) {
	
	$errors = array('errors' => array('error' => 140, 'message' => 'Invalid token')); 

    echo json_encode($errors, JSON_PRETTY_PRINT);

    exit();

}

$user = Wo_UserData($user_id);

if (empty($user) || !is_array($user)) {
	
	$errors = array('errors' => array('error' => 160, 'message' => 'Can\'t recive user data, try again.')); 

    echo json_encode($errors, JSON_PRETTY_PRINT);

    exit();

}

if (Wo_AppHasPermission($user_id, Wo_GetIdFromToken($_GET['access_token'])) === false) {

	$errors = array('errors' => array('error' => 180, 'message' => 'No permission')); 

    echo json_encode($errors, JSON_PRETTY_PRINT);

    exit();

}

$user_data = array(

	'status' => 200,

	'user_data' => array(

		'username' => $user['username'], 

		'email' => $user['email'],

		'name' => $user['name'], 

		'first_name' => $user['first_name'], 

		'last_name' => $user['last_name'],

		'gender' => $user['gender'], 

		'avatar' => $user['avatar'], 

		'cover' => $user['cover']

		)
	);

echo json_encode($user_data, JSON_PRETTY_PRINT);

exit();

?>