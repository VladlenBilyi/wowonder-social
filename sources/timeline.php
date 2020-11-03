<?php
if (isset($_GET['u'])) {
    $check_user = Wo_IsNameExist($_GET['u'], 1);
    if (in_array(true, $check_user)) {
        if ($check_user['type'] == 'user') {
           $id = $user_id = Wo_UserIdFromUsername($_GET['u']);
           $wo['user_profile'] = Wo_UserData($user_id);
           $type = 'timeline';
           $about = $wo['user_profile']['about'];
           $name = $wo['user_profile']['name'];
        } else if ($check_user['type'] == 'page') {
           $id = $page_id = Wo_PageIdFromPagename($_GET['u']);
           $wo['page_profile'] = Wo_PageData($page_id);
           $type = 'page';
           $about = $wo['page_profile']['about'];
           $name = $wo['page_profile']['name'];
        } else if ($check_user['type'] == 'group') {
           $id = $group_id  = Wo_GroupIdFromGroupname($_GET['u']);
           $wo['group_profile'] = Wo_GroupData($group_id);
           $type = 'group';
           $about = $wo['group_profile']['about'];
           $name = $wo['group_profile']['name'];
        }
    } else {
        header("Location: " . Wo_SeoLink('index.php?tab1=404'));
        exit();
    } 
} else {
    header("Location: " . $wo['config']['site_url']);
    exit();
}
if (!empty($_GET['ref'])) {
   if ($_GET['ref'] == 'se') {
      $regsiter_recent = Wo_RegsiterRecent($id, $type);
   }
}
if (Wo_IsLogged() === true && $wo['config']['profileVisit'] == 1 && $type == 'timeline') {
    if ($wo['user_profile']['user_id'] != $wo['user']['user_id'] && $wo['user']['visit_privacy'] == 0) {
      if ($wo['user_profile']['visit_privacy'] == 0) {
        $notification_data_array = array(
            'recipient_id' => $wo['user_profile']['user_id'],
            'type' => 'visited_profile',
            'url' => 'index.php?tab1=timeline&u=' . $wo['user']['username']
        );
        Wo_RegisterNotification($notification_data_array);
      } 
    }
}
$wo['description'] = $about;
$wo['keywords']    = '';
$wo['page']        = $type;
$wo['title']       = $name;
$wo['content']     = Wo_LoadPage("{$type}/content");