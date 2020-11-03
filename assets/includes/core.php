<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
require_once('connect.php');
function Wo_GetConfig() {
      global $sqlConnect;
      $data  = array();
      $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_CONFIG);
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[$fetched_data['name']] = $fetched_data['value'];
      }
      return $data;
}
function Wo_SaveConfig($update_name, $value) {
      global $wo, $config, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!array_key_exists($update_name, $config)) {
            return false;
      }
      $update_name = Wo_Secure($update_name);
      $value       = Wo_Secure($value, 0);
      $query_one   = " UPDATE " . T_CONFIG . " SET `value` = '{$value}' WHERE `name` = '{$update_name}'";
      $query       = mysqli_query($sqlConnect, $query_one);
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_Login($username, $password) {
      global $sqlConnect;
      if (empty($username) || empty($username)) {
            return false;
      }
      $username = Wo_Secure($username);
      $password = Wo_Secure(md5($password));
      $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `username` = '{$username}' AND `password` = '{$password}'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_SetLoginWithSession($user_email) {
      if (empty($user_email)) {
            return false;
      }
      $user_email          = Wo_Secure($user_email);
      $_SESSION['user_id'] = Wo_UserIdFromEmail($user_email);
}
function Wo_UserActive($username) {
      global $sqlConnect;
      if (empty($username)) {
            return false;
      }
      $username = Wo_Secure($username);
      $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . "  WHERE `username`= '{$username}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_UserInactive($username) {
      global $sqlConnect;
      if (empty($username)) {
            return false;
      }
      $username = Wo_Secure($username);
      $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . "  WHERE `username`= '{$username}' AND `active` = '2' ");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_UserExists($username) {
      global $sqlConnect;
      if (empty($username)) {
            return false;
      }
      $username = Wo_Secure($username);
      $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `username` = '{$username}'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_UserIdFromUsername($username) {
      global $sqlConnect;
      if (empty($username)) {
            return false;
      }
      $username = Wo_Secure($username);
      $query    = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$username}'");
      return Wo_Sql_Result($query, 0, 'user_id');
}
function Wo_UserIdFromEmail($email) {
      global $sqlConnect;
      if (empty($email)) {
            return false;
      }
      $email = Wo_Secure($email);
      $query = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_USERS . " WHERE `email` = '{$email}'");
      return Wo_Sql_Result($query, 0, 'user_id');
}
function Wo_IsBlocked($user_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $user_id        = Wo_Secure($user_id);
      $query          = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_BLOCKS . " WHERE (`blocker` = {$user_id} AND `blocked` = {$logged_user_id})");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_EmailExists($email) {
      global $sqlConnect;
      if (empty($email)) {
            return false;
      }
      $email = Wo_Secure($email);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `email` = '{$email}'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_IsOnwer($user_id) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $user_id        = Wo_Secure($user_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsAdmin($logged_user_id) === false) {
            if ($user_id == $logged_user_id) {
                  return true;
            } else {
                  return false;
            }
      } else {
            return true;
      }
}
function Wo_UserData($user_id) {
      global $wo, $sqlConnect, $cache;
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $data           = array();
      $user_id        = Wo_Secure($user_id);
      $query_one      = "SELECT * FROM " . T_USERS . " WHERE `user_id` = {$user_id}";
      $hashed_user_Id = md5($user_id);
      if ($wo['config']['cacheSystem'] == 1) {
            $fetched_data = $cache->read($hashed_user_Id . '_U_Data.tmp');
            if (empty($fetched_data)) {
                  $sql          = mysqli_query($sqlConnect, $query_one);
                  $fetched_data = mysqli_fetch_assoc($sql);
                  $cache->write($hashed_user_Id . '_U_Data.tmp', $fetched_data);
            }
      } else {
            $sql          = mysqli_query($sqlConnect, $query_one);
            $fetched_data = mysqli_fetch_assoc($sql);
      }
      if (empty($fetched_data)) {
            return array();
      }
      $fetched_data['avatar_org'] = $fetched_data['avatar'];
      $fetched_data['cover_org']  = $fetched_data['cover'];
      $explode2 = @end(explode('.', $fetched_data['cover']));
      $explode3 = @explode('.', $fetched_data['cover']);
      $fetched_data['cover_full'] = $wo['userDefaultCover'];
      if ($fetched_data['cover'] != $wo['userDefaultCover']) {
            @$fetched_data['cover_full'] = $explode3[0] . '_full.' . $explode2;
      }
      $fetched_data['avatar'] = Wo_GetMedia($fetched_data['avatar']);
      $fetched_data['cover']  = Wo_GetMedia($fetched_data['cover']);
      $fetched_data['id']     = $fetched_data['user_id'];
      $fetched_data['type']   = 'user';
      $fetched_data['url']    = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['username']);
      $fetched_data['name']   = '';
      if (!empty($fetched_data['first_name'])) {
            if (!empty($fetched_data['last_name'])) {
                  $fetched_data['name'] = $fetched_data['first_name'] . ' ' . $fetched_data['last_name'];
            } else {
                  $fetched_data['name'] = $fetched_data['first_name'];
            }
      } else {
            $fetched_data['name'] = $fetched_data['username'];
      }
      return $fetched_data;
}
function Wo_UserStatus($user_id, $lastseen, $type = '') {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if ($wo['user']['showlastseen'] == 0) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      if (empty($lastseen) || !is_numeric($lastseen) || $lastseen < 0) {
            return false;
      }
      $status   = '';
      $user_id  = Wo_Secure($user_id);
      $lastseen = Wo_Secure($lastseen);
      $time     = time() - 60;
      if ($lastseen < $time) {
            if ($wo['config']['message_typing'] == 1) {
                  if (Wo_IsTyping($user_id)) {
                        Wo_DeleteAllTyping($user_id);
                  }
            }
            if ($type == 'profile') {
                  $status = '<span class="small-last-seen">' . $wo['lang']['last_seen'] . ' <span style="font-size:12px; color:#777;"> ' . Wo_Time_Elapsed_String($lastseen) . '</span></span>';
            } else {
                  $status = '<span class="small-last-seen">' . $wo['lang']['last_seen'] . ' ' . Wo_Time_Elapsed_String($lastseen) . '</span>';
            }
      } else {
            $status = '<span class="online-text"> ' . $wo['lang']['online'] . ' </span>';
      }
      return $status;
}
function Wo_LastSeen($user_id, $type = '') {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      if ($type == 'first') {
            $user = Wo_UserData($user_id);
            if ($user['status'] == 1) {
                  return false;
            }
      } else {
            if ($wo['user']['status'] == 1) {
                  return false;
            }
      }
      $user_id = Wo_Secure($user_id);
      $query   = mysqli_query($sqlConnect, " UPDATE " . T_USERS . " SET `lastseen` = " . time() . " WHERE `user_id` = {$user_id} AND `active` = '1'");
      if ($query) {
            if ($wo['config']['cacheSystem'] == 1) {
                  $cache->delete(md5($user_id) . '_U_Data.tmp');
            }
            return true;
      } else {
            return false;
      }
}
function Wo_RegisterUser($registration_data) {
      global $wo, $sqlConnect;
      if (empty($registration_data)) {
            return false;
      }
      $ip                              = '0.0.0.0';
      $registration_data['registered'] = date('n') . '/' . date("Y");
      if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
      }
      $registration_data['password']   = md5($registration_data['password']);
      $registration_data['ip_address'] = Wo_Secure($ip);
      $fields                          = '`' . implode('`, `', array_keys($registration_data)) . '`';
      $data                            = '\'' . implode('\', \'', $registration_data) . '\'';
      $query                           = mysqli_query($sqlConnect, "INSERT INTO " . T_USERS . " ({$fields}) VALUES ({$data})");
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_ActivateUser($email, $code) {
      global $sqlConnect;
      $email  = Wo_Secure($email);
      $code   = Wo_Secure($code);
      $query  = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`)  FROM " . T_USERS . "  WHERE `email` = '{$email}' AND `email_code` = '{$code}' AND `active` = '0'");
      $result = Wo_Sql_Result($query, 0);
      if ($result == 1) {
            $query_two = mysqli_query($sqlConnect, " UPDATE " . T_USERS . "  SET `active` = '1' WHERE `email` = '{$email}' ");
            if ($query_two) {
                  return true;
            }
      } else {
            return false;
      }
}
function Wo_ResetPassword($user_id, $password) {
      global $sqlConnect;
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      if (empty($password)) {
            return false;
      }
      $user_id  = Wo_Secure($user_id);
      $password = md5($password);
      $query    = mysqli_query($sqlConnect, " UPDATE " . T_USERS . " SET `password` = '{$password}' WHERE `user_id` = {$user_id} ");
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetLanguages() {
      $data           = array();
      $dir            = scandir('assets/languages');
      $languages_name = array_diff($dir, array(
            ".",
            "..",
            "error_log",
            "index.html",
            ".htaccess",
            "_notes"
      ));
      return $languages_name;
}
function Wo_SlugPost($string) {
      $slug = url_slug($string, array(
            'delimiter' => '-',
            'limit' => 80,
            'lowercase' => true,
            'replacements' => array(
                  '/\b(an)\b/i' => 'a',
                  '/\b(example)\b/i' => 'Test'
            )
      ));
      return $slug . '.html';
}
function Wo_GetPostIdFromUrl($string) {
      $slug_string = '';
      $string      = Wo_Secure($string);
      if (preg_match('/[^a-z\s-]/i', $string)) {
            $string_exp  = @explode('_', $string);
            $slug_string = $string_exp[0];
      } else {
            $slug_string = $string;
      }
      return Wo_Secure($slug_string);
}
function Wo_isValidPasswordResetToken($string) {
      global $sqlConnect;
      $string_exp = explode('_', $string);
      $user_id    = Wo_Secure($string_exp[0]);
      $password   = Wo_Secure($string_exp[1]);
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      if (empty($password)) {
            return false;
      }
      $query = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `user_id` = {$user_id} AND `password` = '{$password}' AND `active` = '1' ");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_DeleteUser($user_id) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if ($wo['user']['user_id'] != $user_id) {
                  return false;
            }
      }
      $query_one_delete_photos = mysqli_query($sqlConnect, " SELECT `avatar`,`cover` FROM " . T_USERS . " WHERE `user_id` = {$user_id}");
      $fetched_data            = mysqli_fetch_assoc($query_one_delete_photos);
      if (isset($fetched_data['avatar']) && !empty($fetched_data['avatar']) && $fetched_data['avatar'] != $wo['userDefaultAvatar']) {
            @unlink($fetched_data['avatar']);
      }
      if (isset($fetched_data['cover']) && !empty($fetched_data['cover']) && $fetched_data['cover'] != $wo['userDefaultCover']) {
            @unlink($fetched_data['cover']);
      }
      $query_one_delete_media = mysqli_query($sqlConnect, " SELECT `media` FROM " . T_MESSAGES . " WHERE `from_id` = {$user_id} OR `to_id` = {$user_id}");
      if (mysqli_num_rows($query_one_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_one_delete_media)) {
                  if (isset($fetched_data['media']) && !empty($fetched_data['media'])) {
                        @unlink($fetched_data['media']);
                  }
            }
      }
      $query_two_delete_media = mysqli_query($sqlConnect, " SELECT `postFile`,`id`,`post_id` FROM " . T_POSTS . " WHERE `user_id` = {$user_id}");
      if (mysqli_num_rows($query_two_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_two_delete_media)) {
                  $query_one_reports = mysqli_query($sqlConnect, "DELETE FROM " . T_REPORTS . " WHERE `post_id` = " . $fetched_data['id']);
                  $query_one_reports .= mysqli_query($sqlConnect, "DELETE FROM " . T_REPORTS . " WHERE `post_id` = " . $fetched_data['post_id']);
                  if (isset($fetched_data['postFile']) && !empty($fetched_data['postFile'])) {
                        @unlink($fetched_data['postFile']);
                  }
            }
      }
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($user_id) . '_U_Data.tmp');
            $query_two = mysqli_query($sqlConnect, "SELECT `id`,`post_id` FROM " . T_POSTS . " WHERE `user_id` = {$user_id} OR `recipient_id` = {$user_id}");
            if (mysqli_num_rows($query_two) > 0) {
                  while ($fetched_data_two = mysqli_fetch_assoc($query_two)) {
                        $cache->delete(md5($fetched_data_two['id']) . '_P_Data.tmp');
                        $cache->delete(md5($fetched_data_two['post_id']) . '_P_Data.tmp');
                  }
            }
      }
      $query_four_delete_media = mysqli_query($sqlConnect, "SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id}");
      if (mysqli_num_rows($query_four_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_four_delete_media)) {
                  $delete_posts = Wo_DeletePage($fetched_data['page_id']);
            }
      }
      $query_five_delete_media = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_GROUPS . " WHERE `user_id` = {$user_id}");
      if (mysqli_num_rows($query_five_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_five_delete_media)) {
                  $delete_groups = Wo_DeleteGroup($fetched_data['id']);
            }
      }
      $query_6_delete_media = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_POSTS . " WHERE `user_id` = {$user_id} OR `recipient_id` = {$user_id}");
      if (mysqli_num_rows($query_6_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_6_delete_media)) {
                  $delete_posts = Wo_DeletePost($fetched_data['id']);
            }
      }
      $query_one = mysqli_query($sqlConnect,  "DELETE FROM " . T_USERS . " WHERE `user_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_GAMES_PLAYERS . " WHERE `user_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} OR `following_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_MESSAGES . " WHERE `from_id` = {$user_id} OR `to_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `notifier_id` = {$user_id} OR `recipient_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_REPORTS . " WHERE `user_id` = {$user_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_BLOCKS . " WHERE `block_to` = {$user_id} OR `block_from` = {$user_id}");
      if ($query_one) {
            return true;
      }
}
function Wo_UpdateUserData($user_id, $update_data) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if ($wo['user']['user_id'] != $user_id) {
                  return false;
            }
      }
      if (isset($update_data['verified'])) {
            if (Wo_IsAdmin($wo['user']['user_id']) === false) {
                  return false;
            }
      }
      if (!empty($update_data['relationship'])) {
            if (!array_key_exists($update_data['relationship'], $wo['relationship'])) {
                  $update_data['relationship'] = 1;
            }
      }
      if (!empty($update_data['country_id'])) {
            if (!array_key_exists($update_data['country_id'], $wo['countries_name'])) {
                  $update_data['country_id'] = 1;
            }
      }
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . Wo_Secure($data) . '\'';
      }
      $impload   = implode(', ', $update);
      $query_one = " UPDATE " . T_USERS . " SET {$impload} WHERE `user_id` = {$user_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($user_id) . '_U_Data.tmp');
      }
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetMedia($media) {
      global $wo;
      if (empty($media)) {
            return '';
      }
      return $wo['config']['site_url'] . '/' . $media;
}
function Wo_UploadImage($file, $name, $type, $user_id = 0, $placement = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($file) || empty($name) || empty($type) || empty($user_id)) {
            return false;
      }
      $ext = pathinfo($name, PATHINFO_EXTENSION);
      if (!file_exists('upload/photos/' . date('Y'))) {
            mkdir('upload/photos/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
            mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
      }
      $allowed           = 'jpg,png,jpeg,gif';
      $new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $extension_allowed = explode(',', $allowed);
      $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
      if (!in_array($file_extension, $extension_allowed)) {
            return false;
      }
      $dir = 'upload/photos/' . date('Y') . '/' . date('m');
      if ($placement == 'page') {
            $image_data['page_id'] = Wo_Secure($user_id);
      } else if ($placement == 'group') {
            $image_data['id'] = Wo_Secure($user_id);
      } else {
            $image_data['user_id'] = Wo_Secure($user_id);
      }
      if ($type == 'cover') {
            if ($placement == 'page') {
                  $query_one_delete_cover = mysqli_query($sqlConnect, " SELECT `cover` FROM " . T_PAGES . " WHERE `page_id` = " . $image_data['page_id'] . " AND `active` = '1' ");
            } else if ($placement == 'group') {
                  $query_one_delete_cover = mysqli_query($sqlConnect, " SELECT `cover` FROM " . T_GROUPS . " WHERE `id` = " . $image_data['id'] . " AND `active` = '1'");
            } else {
                  $query_one_delete_cover = mysqli_query($sqlConnect, " SELECT `cover` FROM " . T_USERS . " WHERE `user_id` = " . $image_data['user_id'] . " AND `active` = '1' ");
            }
            $fetched_data        = mysqli_fetch_assoc($query_one_delete_cover);
            $filename            = $dir . '/' . Wo_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '_cover.' . $ext;
            $image_data['cover'] = $filename;
            if (move_uploaded_file($file, $filename)) {
                  $update_data = false;
                  if ($placement == 'page') {
                        $update_data = Wo_UpdatePageData($image_data['page_id'], $image_data);
                  } else if ($placement == 'group') {
                        $update_data = Wo_UpdateGroupData($image_data['id'], $image_data);
                  } else {
                        $update_data = Wo_UpdateUserData($image_data['user_id'], $image_data);
                        if ($update_data) {
                              $explode2  = @end(explode('.', $filename));
                              $explode3  = @explode('.', $filename);
                              $last_file = $explode3[0] . '_full.' . $explode2;
                              @Wo_CompressImage($filename, $last_file, 80);
                              $regsiter_cover_image = Wo_RegisterPost(array(
                                    'user_id' => Wo_Secure($image_data['user_id']),
                                    'postFile' => Wo_Secure($last_file),
                                    'time' => time(),
                                    'postType' => Wo_Secure('profile_cover_picture'),
                                    'postPrivacy' => '0'
                              ));
                        }
                  }
                  if ($update_data == true) {
                        Wo_Resize_Crop_Image(1000, 400, $filename, $filename, 80);
                        return true;
                  }
            }
      } else if ($type == 'avatar') {
            $filename             = $dir . '/' . Wo_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '_avatar.' . $ext;
            $image_data['avatar'] = $filename;
            if (move_uploaded_file($file, $filename)) {
                  if ($placement == 'page') {
                        $update_data = Wo_UpdatePageData($image_data['page_id'], $image_data);
                        Wo_Resize_Crop_Image(400, 400, $filename, $filename, 60);
                        return true;
                  } else if ($placement == 'group') {
                        $update_data = Wo_UpdateGroupData($image_data['id'], $image_data);
                        Wo_Resize_Crop_Image(400, 400, $filename, $filename, 60);
                        return true;
                  } else {
                        if (Wo_UpdateUserData($image_data['user_id'], $image_data)) {
                              $explode2  = @end(explode('.', $filename));
                              $explode3  = @explode('.', $filename);
                              $last_file = $explode3[0] . '_full.' . $explode2;
                              @Wo_CompressImage($filename, $last_file, 80);
                              $regsiter_image = Wo_RegisterPost(array(
                                    'user_id' => Wo_Secure($image_data['user_id']),
                                    'postFile' => Wo_Secure($last_file),
                                    'time' => time(),
                                    'postType' => Wo_Secure('profile_picture'),
                                    'postPrivacy' => '0'
                              ));
                              Wo_Resize_Crop_Image(500, 500, $filename, $filename, 80);
                              return true;
                        }
                  }
            }
      } else if ($type == 'background_image') {
            $query_one_delete_background_image = mysqli_query($sqlConnect, " SELECT `background_image` FROM " . T_USERS . " WHERE `user_id` = " . $image_data['user_id'] . " AND `active` = '1' ");
            $fetched_data                      = mysqli_fetch_assoc($query_one_delete_background_image);
            $filename                          = $dir . '/' . Wo_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '_background_image.' . $ext;
            $image_data['background_image']    = $filename;
            if (move_uploaded_file($file, $filename)) {
                  if (isset($fetched_data['background_image']) && !empty($fetched_data['background_image'])) {
                        @unlink($fetched_data['background_image']);
                  }
                  if (Wo_UpdateUserData($image_data['user_id'], $image_data)) {
                        return true;
                  }
            }
      }
}
function Wo_UserBirthday($birthday) {
      global $wo;
      if (empty($birthday)) {
            return false;
      }
      $birthday = Wo_Secure($birthday);
      $age      = '';
      if ($wo['config']['age'] == 0) {
            $age = date_diff(date_create($birthday), date_create('today'))->y;
      } else {
            $age_style = explode('-', $birthday);
            $age       = $age_style[1] . '/' . $age_style[2] . '/' . $age_style[0];
      }
      return $age;
}
function Wo_GetAllUsers($limit = '', $type = '', $filter = array(), $after = '') {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT `user_id` FROM " . T_USERS . " WHERE `type` = 'user'";
      if (isset($filter) AND !empty($filter)) {
            if (!empty($filter['query'])) {
                  $query_one .= " AND ((`email` LIKE '%" . Wo_Secure($filter['query']) . "%') OR (`username` LIKE '%" . Wo_Secure($filter['query']) . "%') OR CONCAT( `first_name`,  ' ', `last_name` ) LIKE  '%" . Wo_Secure($filter['query']) . "%')";
            }
            if (isset($filter['source']) && $filter['source'] != 'all') {
                  $query_one .= " AND `src` = '" . Wo_Secure($filter['source']) . "'";
            }
            if (isset($filter['status']) && $filter['status'] != 'all') {
                  $query_one .= " AND `active` = '" . Wo_Secure($filter['status']) . "'";
            }
      }
      if (!empty($after) && is_numeric($after) && $after > 0) {
            $query_one .= " AND `user_id` < " . Wo_Secure($after);
      }
      if ($type == 'sidebar') {
            $query_one .= " ORDER BY RAND()";
      } else {
            $query_one .= " ORDER BY `user_id` DESC";
      }
      if (isset($limit) and !empty($limit)) {
            $query_one .= " LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $user_data        = Wo_UserData($fetched_data['user_id']);
            $user_data['src'] = ($user_data['src'] == 'site') ? $wo['config']['siteName'] : $user_data['src'];
            ;
            $data[] = $user_data;
      }
      return $data;
}
function Wo_WelcomeUsers($limit = '', $type = '') {
      global $wo, $sqlConnect;
      $limit     = 12;
      $data      = array();
      $query_one = " SELECT `user_id` FROM " . T_USERS . " WHERE `active` = '1' AND `avatar` <> '" . Wo_Secure($wo['userDefaultAvatar']) . "' ORDER BY RAND() LIMIT {$limit}";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_UserSug($limit = 20) {
      global $wo, $sqlConnect;
      if (!is_numeric($limit)) {
            return false;
      }
      $data      = array();
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $query_one = " SELECT `user_id` FROM " . T_USERS . " WHERE `active` = '1' AND `user_id` NOT IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id}) AND `user_id` <> {$user_id}";
      if (isset($limit)) {
            $query_one .= " ORDER BY RAND() LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_ImportImageFromLogin($media) {
      if (!file_exists('upload/photos/' . date('Y'))) {
            mkdir('upload/photos/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
            mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
      }
      $dir         = 'upload/photos/' . date('Y') . '/' . date('m');
      $file_dir    = $dir . '/' . Wo_GenerateKey() . '_avatar.jpg';
      $importImage = @file_put_contents($file_dir, file_get_contents($media));
      if ($importImage) {
            Wo_Resize_Crop_Image(400, 400, $file_dir, $file_dir, 100);
      }
      if (file_exists($file_dir)) {
            return $file_dir;
      } else {
            return $wo['userDefaultAvatar'];
      }
}
function Wo_ImportImageFromUrl($media) {
      if (empty($media)) {
            return $wo['userDefaultAvatar'];
      }
      if (!file_exists('upload/photos/' . date('Y'))) {
            mkdir('upload/photos/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
            mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
      }
      $size      = getimagesize($media);
      $extension = image_type_to_extension($size[2]);
      if (empty($extension)) {
            $extension = '.jpg';
      }
      $dir         = 'upload/photos/' . date('Y') . '/' . date('m');
      $file_dir    = $dir . '/' . Wo_GenerateKey() . '_url_image' . $extension;
      $importImage = @file_put_contents($file_dir, file_get_contents($media));
      if ($importImage) {
            Wo_Resize_Crop_Image(400, 400, $file_dir, $file_dir, 80);
      }
      if (file_exists($file_dir)) {
            return $file_dir;
      } else {
            return $wo['userDefaultAvatar'];
      }
}
function Wo_IsFollowing($following_id, $user_id = 0) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($following_id) || !is_numeric($following_id) || $following_id < 0) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            $user_id = $wo['user']['user_id'];
      }
      $following_id = Wo_Secure($following_id);
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, " SELECT COUNT(`id`) FROM " . T_FOLLOWERS . " WHERE `following_id` = {$following_id} AND `follower_id` = {$user_id} AND `active` = '1' ");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_RegisterFollow($following_id = 0, $follower_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
            return false;
      }
      if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            return false;
      }
      $following_id = Wo_Secure($following_id);
      $follower_id  = Wo_Secure($follower_id);
      $active       = 1;
      if (Wo_IsFollowing($following_id, $follower_id) === true) {
            return false;
      }
      $follower_data  = Wo_UserData($follower_id);
      $following_data = Wo_UserData($following_id);
      if ($following_data['confirm_followers'] == 1) {
            $active = 0;
      }
      if ($wo['config']['connectivitySystem'] == 1) {
            $active = 0;
      }
      $query = mysqli_query($sqlConnect, " INSERT INTO " . T_FOLLOWERS . " (`following_id`,`follower_id`,`active`) VALUES ({$following_id},{$follower_id},'{$active}')");
      if ($query) {
            if ($active == 1) {
                  $notification_data = array(
                        'recipient_id' => $following_id,
                        'type' => 'following',
                        'url' => 'index.php?tab1=timeline&u=' . $follower_data['username']
                  );
                  Wo_RegisterNotification($notification_data);
            }
            return true;
      }
}
function Wo_CountFollowRequests($data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $get     = array();
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (empty($data['account_id']) || $data['account_id'] == 0) {
            $data['account_id'] = $user_id;
            $account            = $wo['user'];
      }
      if (!is_numeric($data['account_id']) || $data['account_id'] < 1) {
            return false;
      }
      if ($data['account_id'] != $user_id) {
            $data['account_id'] = Wo_Secure($data['account_id']);
            $account            = Wo_UserData($data['account_id']);
      }
      $query_one = " SELECT COUNT(`id`) AS `FollowRequests` FROM " . T_FOLLOWERS . " WHERE `active` = '0' AND following_id =  " . $account['user_id'];
      if (isset($data['unread']) && $data['unread'] == true) {
            $query_one .= " AND `seen` = 0";
      }
      $query_one .= " ORDER BY `id` DESC";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
      return $sql_fetch_one['FollowRequests'];
}
function Wo_IsFollowRequested($following_id = 0, $follower_id = 0) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
            return false;
      }
      if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            $follower_id = $wo['user']['user_id'];
      }
      if (!is_numeric($follower_id) or $follower_id < 1) {
            return false;
      }
      $following_id = Wo_Secure($following_id);
      $follower_id  = Wo_Secure($follower_id);
      $query        = "SELECT `id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$follower_id} AND `following_id` = {$following_id} AND `active` = '0'";
      $sql_query    = mysqli_query($sqlConnect, $query);
      if (mysqli_num_rows($sql_query) > 0) {
            return true;
      }
}
function Wo_DeleteFollow($following_id = 0, $follower_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
            return false;
      }
      if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            return false;
      }
      $following_id = Wo_Secure($following_id);
      $follower_id  = Wo_Secure($follower_id);
      if (Wo_IsFollowing($following_id, $follower_id) === false && Wo_IsFollowRequested($following_id, $follower_id) === false) {
            return false;
      } else {
            $query = mysqli_query($sqlConnect, " DELETE FROM " . T_FOLLOWERS . " WHERE `following_id` = {$following_id} AND `follower_id` = {$follower_id}");
            if ($wo['config']['connectivitySystem'] == 1) {
                  $query_two     = "DELETE FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$following_id} AND `following_id` = {$follower_id}";
                  $sql_query_two = mysqli_query($sqlConnect, $query_two);
            }
            if ($query) {
                  return true;
            }
      }
}
function Wo_CountFollowing($user_id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) AS count FROM " . T_USERS . " WHERE `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `following_id` <> {$user_id} AND `active` = '1') AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_AcceptFollowRequest($following_id = 0, $follower_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
            return false;
      }
      if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            return false;
      }
      $following_id = Wo_Secure($following_id);
      $follower_id  = Wo_Secure($follower_id);
      if (Wo_IsFollowRequested($following_id, $follower_id) === false) {
            return false;
      }
      $follower_data = Wo_UserData($follower_id);
      $query         = mysqli_query($sqlConnect, "UPDATE " . T_FOLLOWERS . " SET `active` = '1' WHERE `following_id` = {$follower_id} AND `follower_id` = {$following_id} AND `active` = '0'");
      if ($wo['config']['connectivitySystem'] == 1) {
            $query_two = mysqli_query($sqlConnect, "INSERT INTO " . T_FOLLOWERS . " (`following_id`,`follower_id`,`active`) VALUES ({$following_id},{$follower_id},'1') ");
      }
      if ($query) {
            $notification_data = array(
                  'recipient_id' => $following_id,
                  'type' => 'accepted_request',
                  'url' => 'index.php?tab1=timeline&u=' . $follower_data['username']
            );
            if (Wo_RegisterNotification($notification_data) === true) {
                  return true;
            } else {
                  return false;
            }
      }
}
function Wo_DeleteFollowRequest($following_id, $follower_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
            return false;
      }
      if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            return false;
      }
      $following_id = Wo_Secure($following_id);
      $follower_id  = Wo_Secure($follower_id);
      if (Wo_IsFollowRequested($following_id, $follower_id) === false) {
            return false;
      } else {
            $query = mysqli_query($sqlConnect, " DELETE FROM " . T_FOLLOWERS . " WHERE `following_id` = {$follower_id} AND `follower_id` = {$following_id} ");
            if ($query) {
                  return true;
            }
      }
}
function Wo_GetFollowRequests($user_id = 0, $search_query = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data = array();
      if (empty($user_id) or $user_id == 0) {
            $user_id = $wo['user']['user_id'];
      }
      if (!is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      $query   = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `follower_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` <> {$user_id} AND `following_id` = {$user_id} AND `active` = '0') AND `active` = '1' ";
      if (!empty($search_query)) {
            $search_query = Wo_Secure($search_query);
            $query .= " AND `name` LIKE '%$search_query%'";
      }
      $query .= " ORDER BY `user_id` DESC";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($sql_fetch = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_UserData($sql_fetch['user_id']);
      }
      return $data;
}
function Wo_CountFollowers($user_id) {
      global $wo, $sqlConnect;
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $data         = array();
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`) AS count FROM " . T_USERS . " WHERE `user_id` IN (SELECT `follower_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` <> {$user_id} AND `following_id` = {$user_id} AND `active` = '1') AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_GetFollowing($user_id, $type = '', $limit = '', $after_user_id = '') {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id       = Wo_Secure($user_id);
      $after_user_id = Wo_Secure($after_user_id);
      $query         = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `following_id` <> {$user_id} AND `active` = '1') AND `active` = '1' ";
      if (!empty($after_user_id) && is_numeric($after_user_id)) {
            $query .= " AND `user_id` < {$after_user_id}";
      }
      if ($type == 'sidebar' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY RAND() LIMIT {$limit}";
      }
      if ($type == 'profile' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY `user_id` DESC LIMIT {$limit}";
      }
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetFollowers($user_id, $type = '', $limit = '', $after_user_id = '') {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id       = Wo_Secure($user_id);
      $after_user_id = Wo_Secure($after_user_id);
      $query         = " SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `follower_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` <> {$user_id} AND `following_id` = {$user_id} AND `active` = '1') AND `active` = '1'";
      if (!empty($after_user_id) && is_numeric($after_user_id)) {
            $query .= " AND `user_id` < {$after_user_id}";
      }
      if ($type == 'sidebar' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY RAND()";
      }
      if ($type == 'profile' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY `user_id` DESC";
      }
      $query .= " LIMIT {$limit} ";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetFollowButton($user_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!is_numeric($user_id) or $user_id < 0) {
            return false;
      }
      if ($user_id == $wo['user']['user_id']) {
            return false;
      }
      $account = $wo['follow'] = Wo_UserData($user_id);
      if (!isset($wo['follow']['user_id'])) {
            return false;
      }
      $user_id           = Wo_Secure($user_id);
      $logged_user_id    = Wo_Secure($wo['user']['user_id']);
      $follow_button     = 'buttons/follow';
      $unfollow_button   = 'buttons/unfollow';
      $add_frined_button = 'buttons/add-friend';
      $unfrined_button   = 'buttons/unfriend';
      $accept_button     = 'buttons/accept-request';
      $request_button    = 'buttons/requested';
      if (Wo_IsFollowing($user_id, $logged_user_id)) {
            if ($wo['config']['connectivitySystem'] == 1) {
                  return Wo_LoadPage($unfrined_button);
            } else {
                  return Wo_LoadPage($unfollow_button);
            }
      } else {
            if (Wo_IsFollowRequested($user_id, $logged_user_id)) {
                  return Wo_LoadPage($request_button);
            } else if (Wo_IsFollowRequested($logged_user_id, $user_id)) {
                  return Wo_LoadPage($accept_button);
            } else {
                  if ($account['follow_privacy'] == 1) {
                        if (Wo_IsFollowing($logged_user_id, $user_id)) {
                              if ($wo['config']['connectivitySystem'] == 1) {
                                    return Wo_LoadPage($add_frined_button);
                              } else {
                                    return Wo_LoadPage($follow_button);
                              }
                        }
                  } else if ($account['follow_privacy'] == 0) {
                        if ($wo['config']['connectivitySystem'] == 1) {
                              return Wo_LoadPage($add_frined_button);
                        } else {
                              return Wo_LoadPage($follow_button);
                        }
                  }
            }
      }
}
function Wo_RegisterNotification($data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($data['recipient_id']) or empty($data['recipient_id']) or !is_numeric($data['recipient_id']) or $data['recipient_id'] < 1) {
            return false;
      }
      if (!isset($data['post_id']) or empty($data['post_id'])) {
            $data['post_id'] = 0;
      }
      if (!is_numeric($data['post_id']) or $data['recipient_id'] < 0) {
            return false;
      }
      if (empty($data['notifier_id']) or $data['notifier_id'] == 0) {
            $data['notifier_id'] = Wo_Secure($wo['user']['user_id']);
      }
      if (!is_numeric($data['notifier_id']) or $data['notifier_id'] < 1) {
            return false;
      }
      if ($data['notifier_id'] == $wo['user']['user_id']) {
            $notifier = $wo['user'];
      } else {
            $data['notifier_id'] = Wo_Secure($data['notifier_id']);
            $notifier            = Wo_UserData($data['notifier_id']);
            if (!isset($notifier['user_id'])) {
                  return false;
            }
      }
      if ($notifier['user_id'] != $wo['user']['user_id']) {
            return false;
      }
      if ($data['recipient_id'] == $data['notifier_id']) {
            return false;
      }
      if (!isset($data['text'])) {
            $data['text'] = '';
      }
      if (!isset($data['type']) or empty($data['type'])) {
            return false;
      }
      if (!isset($data['url']) or empty($data['url'])) {
            return false;
      }
      $recipient = Wo_UserData($data['recipient_id']);
      if (!isset($recipient['user_id'])) {
            return false;
      }
      $url                  = $data['url'];
      $recipient['user_id'] = Wo_Secure($recipient['user_id']);
      $data['post_id']      = Wo_Secure($data['post_id']);
      $data['type']         = Wo_Secure($data['type']);
      if (!empty($data['type2'])) {
            $data['type2'] = Wo_Secure($data['type2']);
      } else {
            $data['type2'] = '';
      }
      if ($data['text'] != strip_tags($data['text'])) {
            $data['text'] = '';
      }
      $data['text']            = Wo_Secure($data['text']);
      $notifier['user_id']     = Wo_Secure($notifier['user_id']);
      $page_notifcation_query  = '';
      $page_notifcation_query2 = '';
      if (!empty($data['page_id']) && $data['page_id'] > 0) {
            $page = Wo_PageData($data['page_id']);
            if (!isset($page['page_id'])) {
                  return false;
            }
            $page_id = Wo_Secure($page['page_id']);
            if (isset($data['page_enable'])) {
                  if ($data['page_enable'] !== false) {
                        $notifier['user_id'] = 0;
                  }
            } else {
                  $notifier['user_id'] = 0;
            }
            $page_notifcation_query  = '`page_id`,';
            $page_notifcation_query2 = "{$page_id}, ";
      }
      $group_notifcation_query  = '';
      $group_notifcation_query2 = '';
      if (!empty($data['group_id']) && $data['group_id'] > 0) {
            $group = Wo_GroupData($data['group_id']);
            if (!isset($group['id'])) {
            }
            $group_id                 = Wo_Secure($group['id']);
            $group_notifcation_query  = '`group_id`,';
            $group_notifcation_query2 = "{$group_id}, ";
      }
      $query_one     = " SELECT `id` FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $recipient['user_id'] . " AND `post_id` = " . $data['post_id'] . " AND `type` = '" . $data['type'] . "'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) > 0) {
            if ($data['type'] != "following") {
                  $query_two     = " DELETE FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $recipient['user_id'] . " AND `post_id` = " . $data['post_id'] . " AND `type` = '" . $data['type'] . "'";
                  $sql_query_two = mysqli_query($sqlConnect, $query_two);
            }
      }
      if (!isset($data['undo']) or $data['undo'] != true) {
            $query_three     = "INSERT INTO " . T_NOTIFICATION . " (`recipient_id`, `notifier_id`, {$page_notifcation_query} {$group_notifcation_query} `post_id`, `type`, `type2`, `text`, `url`, `time`) VALUES (" . $recipient['user_id'] . "," . $notifier['user_id'] . ",{$page_notifcation_query2} {$group_notifcation_query2} " . $data['post_id'] . ",'" . $data['type'] . "','" . $data['type2'] . "','" . $data['text'] . "','{$url}'," . time() . ")";
            $sql_query_three = mysqli_query($sqlConnect, $query_three);
            if ($sql_query_three) {
                  if ($wo['config']['emailNotification'] == 1 && $recipient['emailNotification'] == 1) {
                        $send_mail = false;
                        if ($data['type'] == 'liked_post' && $recipient['e_liked'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'share_post' && $recipient['e_shared'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'comment' && $recipient['e_commented'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'following' && $recipient['e_followed'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'wondered_post' && $recipient['e_wondered'] == 1) {
                              $send_mail = true;
                        }
                        if (($data['type'] == 'comment_mention' || $data['type'] == 'post_mention') && $recipient['e_mentioned'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'accepted_request' && $recipient['e_accepted'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'visited_profile' && $recipient['e_visited'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'joined_group' && $recipient['e_joined_group'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'liked_page' && $recipient['e_liked_page'] == 1) {
                              $send_mail = true;
                        }
                        if ($data['type'] == 'profile_wall_post' && $recipient['e_profile_wall_post'] == 1) {
                              $send_mail = true;
                        }
                        if ($send_mail == true) {
                              $post_data_id      = Wo_PostData($data['post_id']);
                              $post_data['text'] = '';
                              if (!empty($post_data_id['postText'])) {
                                    $post_data['text'] = substr($post_data_id['postText'], 0, 20);
                              }
                              $data['notifier']        = $notifier;
                              $data['url']             = Wo_SeoLink($url);
                              $data['post_data']       = $post_data;
                              $wo['emailNotification'] = $data;
                              $body                    = Wo_LoadPage('emails/notifiction-email');
                              $headers                 = "From: " . $wo['config']['siteName'] . " <" . $wo['config']['siteEmail'] . ">\r\n";
                              $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                              @mail($recipient['email'], 'New notification', $body, $headers);
                        }
                  }
                  return true;
            }
      }
}
function Wo_GetNotifications($data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $get = array();
      if (!isset($data['account_id']) or empty($data['account_id'])) {
            $data['account_id'] = $wo['user']['user_id'];
      }
      if (!is_numeric($data['account_id']) or $data['account_id'] < 1) {
            return false;
      }
      if ($data['account_id'] == $wo['user']['user_id']) {
            $account = $wo['user'];
      } else {
            $data['account_id'] = $data['account_id'];
            $account            = Wo_UserData($data['account_id']);
      }
      if ($account['user_id'] != $wo['user']['user_id']) {
            return false;
      }
      $new_notif = Wo_CountNotifications(array(
            'unread' => true
      ));
      if ($new_notif > 0) {
            $query_4 = '';
            if (isset($data['type_2']) && !empty($data['type_2'])) {
                  if ($data['type_2'] == 'popunder') {
                        $timepopunder = time() - 60;
                        $query_4      = ' AND `seen_pop` = 0 AND `time` >= ' . $timepopunder;
                  }
            }
            $query_one = " SELECT * FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $account['user_id'] . " AND `seen` = 0 {$query_4} ORDER BY `id` DESC";
      } else {
            $query_one = " SELECT * FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $account['user_id'];
            if (isset($data['unread']) && $data['unread'] == true) {
                  $query_one .= " AND `seen` = 0";
            }
            $query_one .= " ORDER BY `id` DESC LIMIT 15";
      }
      if (isset($data['all']) && $data['all'] == true) {
            $query_one = "SELECT * FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $account['user_id'] . " AND `seen` = 0 ORDER BY `id` DESC LIMIT 20";
      }
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) > 0) {
            while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
                  if (!empty($sql_fetch_one['page_id']) && empty($sql_fetch_one['notifier_id'])) {
                        $sql_fetch_one['notifier']        = Wo_PageData($sql_fetch_one['page_id']);
                        $sql_fetch_one['notifier']['url'] = Wo_SeoLink('index.php?tab1=timeline&u=' . $sql_fetch_one['notifier']['page_name']);
                  } else {
                        $sql_fetch_one['notifier']        = Wo_UserData($sql_fetch_one['notifier_id']);
                        $sql_fetch_one['notifier']['url'] = Wo_SeoLink('index.php?tab1=timeline&u=' . $sql_fetch_one['notifier']['username']);
                  }
                  $sql_fetch_one['url'] = Wo_SeoLink($sql_fetch_one['url']);
                  $get[]                = $sql_fetch_one;
            }
      }
      mysqli_query($sqlConnect, " DELETE FROM " . T_NOTIFICATION . " WHERE `time` < " . (time() - (60 * 60 * 24 * 5)) . " AND `seen` > 0");
      return $get;
}
function Wo_CountNotifications($data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $get = array();
      if (empty($data['account_id']) or $data['account_id'] == 0) {
            $data['account_id'] = Wo_Secure($wo['user']['user_id']);
            $account            = $wo['user'];
      }
      if (!is_numeric($data['account_id']) or $data['account_id'] < 1) {
            return false;
      }
      if ($data['account_id'] != $wo['user']['user_id']) {
            $data['account_id'] = Wo_Secure($data['account_id']);
            $account            = Wo_UserData($data['account_id']);
      }
      $query_one = " SELECT COUNT(`id`) AS `notifications` FROM " . T_NOTIFICATION . " WHERE `recipient_id` = " . $account['user_id'];
      if (isset($data['unread']) && $data['unread'] == true) {
            $query_one .= " AND `seen` = 0";
      }
      $query_one .= " ORDER BY `id` DESC";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
      return $sql_fetch_one['notifications'];
}
function Wo_GetSearch($search_qeury) {
      global $sqlConnect;
      $search_qeury = Wo_Secure($search_qeury);
      $data         = array();
      $query        = mysqli_query($sqlConnect, " SELECT `user_id` FROM " . T_USERS . " WHERE ((`username` LIKE '%$search_qeury%') OR CONCAT( `first_name`,  ' ', `last_name` ) LIKE '%$search_qeury%') AND `active` = '1' LIMIT 3");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      $query = mysqli_query($sqlConnect, " SELECT `page_id` FROM " . T_PAGES . " WHERE ((`page_name` LIKE '%$search_qeury%') OR `page_title` LIKE '%$search_qeury%') AND `active` = '1' LIMIT 3");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_PageData($fetched_data['page_id']);
      }
      $query = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_GROUPS . " WHERE ((`group_name` LIKE '%$search_qeury%') OR `group_title` LIKE '%$search_qeury%') AND `active` = '1' LIMIT 3");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_GroupData($fetched_data['id']);
      }
      return $data;
}
function Wo_GetRecentSerachs() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $data    = array();
      $query   = mysqli_query($sqlConnect, "SELECT `search_id`,`search_type` FROM " . T_RECENT_SEARCHES . " WHERE `user_id` = {$user_id} ORDER BY `id` DESC LIMIT 10");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            if ($fetched_data['search_type'] == 'user') {
                  $fetched_data_2 = Wo_UserData($fetched_data['search_id']);
            } else if ($fetched_data['search_type'] == 'page') {
                  $fetched_data_2 = Wo_PageData($fetched_data['search_id']);
            } else if ($fetched_data['search_type'] == 'group') {
                  $fetched_data_2 = Wo_GroupData($fetched_data['search_id']);
            } else {
                  return false;
            }
            $data[] = $fetched_data_2;
      }
      return $data;
}
function Wo_GetSearchFilter($result, $limit = 30) {
      global $wo, $sqlConnect;
      $data = array();
      $time = time() - 60;
      if (empty($result)) {
            return array();
      }
      if (!empty($result['query'])) {
            $result['query'] = Wo_Secure($result['query']);
      }
      if (!empty($result['country'])) {
            $result['country'] = Wo_Secure($result['country']);
      }
      if (!empty($result['gender'])) {
            $result['gender'] = Wo_Secure($result['gender']);
      }
      if (!empty($result['status'])) {
            $result['status'] = Wo_Secure($result['status']);
      }
      if (!empty($result['image'])) {
            $result['image'] = Wo_Secure($result['image']);
      }
      $query = " SELECT `user_id` FROM " . T_USERS . " WHERE (`username` LIKE '%" . $result['query'] . "%' OR CONCAT( `first_name`,  ' ', `last_name` ) LIKE  '%" . $result['query'] . "%')";
      if (isset($result['gender'])) {
            $result['gender'] = Wo_Secure($result['gender']);
            if ($result['gender'] == 'male') {
                  $query .= " AND (`gender` = 'male') ";
            } else if ($result['gender'] == 'female') {
                  $query .= " AND (`gender` = 'female') ";
            }
      }
      if (isset($result['country'])) {
            $result['country'] = Wo_Secure($result['country']);
            if ($result['country'] != 'all') {
                  $query .= " AND (`country_id` = " . $result['country'] . ') ';
            }
      }
      if (isset($result['status'])) {
            $result['status'] = Wo_Secure($result['status']);
            if ($result['status'] == 'on') {
                  $query .= " AND (`lastseen` >= {$time}) ";
            } else if ($result['status'] == 'off') {
                  $query .= " AND (`lastseen` <= {$time}) ";
            }
      }
      if (isset($result['image'])) {
            $result['image'] = Wo_Secure($result['image']);
            $d_image         = Wo_Secure($wo['userDefaultAvatar']);
            if ($result['image'] == 'yes') {
                  $query .= " AND (`avatar` <> '{$d_image}') ";
            } else if ($result['image'] == 'no') {
                  $query .= " AND (`avatar` = '{$d_image}') ";
            }
      }
      if (Wo_IsLogged() === true) {
            $user_id = Wo_Secure($wo['user']['user_id']);
            $query .= " AND `user_id` <> {$user_id}";
      }
      $query .= " AND `active` = '1' ";
      if (!empty($limit)) {
            $limit = Wo_Secure($limit);
            $query .= " ORDER BY `first_name` LIMIT {$limit}";
      }
      $sql_query_one = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetMessagesUsers($user_id, $searchQuery = '', $limit = 50, $new = false, $update = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      if (!isset($user_id)) {
            $user_id = $wo['user']['user_id'];
      }
      $data     = array();
      $excludes = array();
      if (isset($searchQuery) AND !empty($searchQuery)) {
            $query_one = " SELECT `user_id` FROM " . T_USERS . " WHERE (`user_id` IN (SELECT `from_id` FROM " . T_MESSAGES . " WHERE `to_id` = {$user_id} AND `active` = '1' ";
            if (isset($new) && $new == true) {
                  $query_one .= " AND `seen` = 0";
            }
            $query_one .= " ORDER BY `user_id` DESC)";
            if (!isset($new) or $new == false) {
                  $query_one .= " OR `user_id` IN (SELECT `to_id` FROM " . T_MESSAGES . " WHERE `from_id` = {$user_id} ORDER BY `id` DESC)";
            }
            $query_one .= ") AND ((`username` LIKE '%{$searchQuery}%') OR CONCAT( `first_name`,  ' ', `last_name` ) LIKE  '%{$searchQuery}%')";
      } else {
            $query_one = "SELECT `user_id` FROM " . T_USERS . " WHERE (`user_id` IN (SELECT `from_id` FROM " . T_MESSAGES . " WHERE `to_id` = {$user_id} AND `active` = '1'";
            if (isset($new) && $new == true) {
                  $query_one .= " AND `seen` = 0";
            }
            $query_one .= " ORDER BY `user_id` DESC)";
            if (!isset($new) or $new == false) {
                  $query_one .= " OR `user_id` IN (SELECT `to_id` FROM " . T_MESSAGES . " WHERE `from_id` = {$user_id} ORDER BY `id` DESC)";
            }
            $query_one .= ")";
      }
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) > 0) {
            while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
                  $data[]     = Wo_UserData($sql_fetch_one['user_id']);
                  $excludes[] = $sql_fetch_one['user_id'];
            }
      }
      $exclude_query_string = 0;
      $exclude_i            = 0;
      $excludes_num         = count($excludes);
      if ($excludes_num > 0) {
            $exclude_query_string = '';
            foreach ($excludes as $exclude) {
                  $exclude_i++;
                  $exclude_query_string .= $exclude;
                  if ($exclude_i != $excludes_num) {
                        $exclude_query_string .= ',';
                  }
            }
      }
      $query_two = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `following_id` NOT IN ({$user_id}, {$exclude_query_string})) AND `active` = '1'";
      if (!empty($searchQuery)) {
            $query_two .= " AND ((`username` LIKE '%$searchQuery%') OR CONCAT( first_name,  ' ', last_name ) LIKE  '%{$searchQuery}%')";
      }
      $query_two .= " ORDER BY `lastseen` DESC LIMIT {$limit}";
      $sql_query_two = mysqli_query($sqlConnect, $query_two);
      while ($sql_fetch_two = mysqli_fetch_assoc($sql_query_two)) {
            $data[] = Wo_UserData($sql_fetch_two['user_id']);
      }
      return $data;
}
function Wo_GetMessages($data = array(), $limit = 50) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $message_data   = array();
      $user_id        = Wo_Secure($data['user_id']);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $query_one = " SELECT * FROM " . T_MESSAGES;
      if (isset($data['new']) && $data['new'] == true) {
            $query_one .= " WHERE `seen` = 0 AND `from_id` = {$user_id} AND `to_id` = {$logged_user_id}";
      } else {
            $query_one .= " WHERE ((`from_id` = {$user_id} AND `to_id` = {$logged_user_id}) OR (`from_id` = {$logged_user_id} AND `to_id` = {$user_id}))";
      }
      if (!empty($data['message_id'])) {
            $data['message_id'] = Wo_Secure($data['message_id']);
            $query_one .= " AND `id` = " . $data['message_id'];
      } else if (!empty($data['before_message_id']) && is_numeric($data['before_message_id']) && $data['before_message_id'] > 0) {
            $data['before_message_id'] = Wo_Secure($data['before_message_id']);
            $query_one .= " AND `id` < " . $data['before_message_id'] . " AND `id` <> " . $data['before_message_id'];
      } else if (!empty($data['after_message_id']) && is_numeric($data['after_message_id']) && $data['after_message_id'] > 0) {
            $data['after_message_id'] = Wo_Secure($data['after_message_id']);
            $query_one .= " AND `id` > " . $data['after_message_id'] . " AND `id` <> " . $data['after_message_id'];
      }
      $sql_query_one    = mysqli_query($sqlConnect, $query_one);
      $query_limit_from = mysqli_num_rows($sql_query_one) - 50;
      if ($query_limit_from < 1) {
            $query_limit_from = 0;
      }
      if (isset($limit)) {
            $query_one .= " ORDER BY `id` ASC LIMIT {$query_limit_from}, 50";
      }
      $query = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $fetched_data['messageUser'] = Wo_UserData($fetched_data['from_id']);
            $fetched_data['text']        = Wo_Markup($fetched_data['text']);
            $fetched_data['text']        = Wo_Emo($fetched_data['text']);
            $fetched_data['onwer']       = ($fetched_data['messageUser']['user_id'] == $wo['user']['user_id']) ? 1 : 0;
            $message_data[]              = $fetched_data;
            if ($fetched_data['messageUser']['user_id'] == $user_id && $fetched_data['seen'] == 0) {
                  mysqli_query($sqlConnect, " UPDATE " . T_MESSAGES . " SET `seen` = " . time() . " WHERE `id` = " . $fetched_data['id']);
            }
      }
      return $message_data;
}
function Wo_RegisterMessage($ms_data = array()) {
      global $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($ms_data)) {
            return false;
      }
      if (empty($ms_data['to_id']) || !is_numeric($ms_data['to_id']) || $ms_data['to_id'] < 0) {
            return false;
      }
      if (empty($ms_data['from_id']) || !is_numeric($ms_data['from_id']) || $ms_data['from_id'] < 0) {
            return false;
      }
      if (empty($ms_data['text']) || !isset($ms_data['text']) || strlen($ms_data['text']) < 0) {
            if (empty($ms_data['media']) || !isset($ms_data['media']) || strlen($ms_data['media']) < 0) {
                  return false;
            }
      }
      $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
      $i          = 0;
      preg_match_all($link_regex, $ms_data['text'], $matches);
      foreach ($matches[0] as $match) {
            $match_url       = strip_tags($match);
            $syntax          = '[a]' . urlencode($match_url) . '[/a]';
            $ms_data['text'] = str_replace($match, $syntax, $ms_data['text']);
      }
      $mention_regex = '/@([A-Za-z0-9_]+)/i';
      preg_match_all($mention_regex, $ms_data['text'], $matches);
      foreach ($matches[1] as $match) {
            $match         = Wo_Secure($match);
            $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
            $match_search  = '@' . $match;
            $match_replace = '@[' . $match_user['user_id'] . ']';
            if (isset($match_user['user_id'])) {
                  $ms_data['text'] = str_replace($match_search, $match_replace, $ms_data['text']);
                  $mentions[]      = $match_user['user_id'];
            }
      }
      $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
      preg_match_all($hashtag_regex, $ms_data['text'], $matches);
      foreach ($matches[1] as $match) {
            if (!is_numeric($match)) {
                  $hashdata = Wo_GetHashtag($match);
                  if (is_array($hashdata)) {
                        $match_search      = '#' . $match;
                        $match_replace     = '#[' . $hashdata['id'] . ']';
                        $ms_data['text']   = str_replace($match_search, $match_replace, $ms_data['text']);
                        $hashtag_query     = " UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                        $hashtag_sql_query = mysqli_query($sqlConnect, $hashtag_query);
                  }
            }
      }
      $fields = '`' . implode('`, `', array_keys($ms_data)) . '`';
      $data   = '\'' . implode('\', \'', $ms_data) . '\'';
      $query  = mysqli_query($sqlConnect, " INSERT INTO " . T_MESSAGES . " ({$fields}) VALUES ({$data})");
      if ($query) {
            $message_id = mysqli_insert_id($sqlConnect);
            return $message_id;
      } else {
            return false;
      }
}
function Wo_DeleteMessage($message_id, $media = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($message_id) || !is_numeric($message_id) || $message_id < 0) {
            return false;
      }
      $message_id    = Wo_Secure($message_id);
      $query_one     = " SELECT `id`, `from_id`, `media` FROM " . T_MESSAGES . " WHERE `id` = {$message_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            $user_fetch    = Wo_UserData($sql_fetch_one['from_id']);
            $continue      = false;
            if ($user_fetch['user_id'] == $wo['user']['user_id']) {
                  $continue = true;
            }
            if ($continue == true) {
                  $query = mysqli_query($sqlConnect, " DELETE FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
                  if ($query) {
                        if (isset($sql_fetch_one['media']) AND !empty($sql_fetch_one['media'])) {
                              @unlink($sql_fetch_one['media']);
                        }
                        return true;
                  } else {
                        return false;
                  }
            } else {
                  return false;
            }
      }
}
function Wo_CountMessages($data = array(), $type = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($data['user_id']) or $data['user_id'] == 0) {
            $data['user_id'] = $wo['user']['user_id'];
      }
      if (!is_numeric($data['user_id']) or $data['user_id'] < 1) {
            return false;
      }
      $data['user_id'] = Wo_Secure($data['user_id']);
      if ($type == 'interval') {
            $account = $wo['user'];
      } else {
            $account = Wo_UserData($data['user_id']);
      }
      if (empty($account['user_id'])) {
            return false;
      }
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      if (isset($data['user_id']) && is_numeric($data['user_id']) && $data['user_id'] > 0) {
            $user_id = Wo_Secure($data['user_id']);
            if (isset($data['new']) && $data['new'] == true) {
                  $query = " SELECT COUNT(`id`) AS `messages` FROM " . T_MESSAGES . " WHERE `to_id` = {$logged_user_id}";
                  if ($wo['user']['user_id'] != $user_id) {
                        $query .= " AND `from_id` = {$user_id}";
                  }
            } else {
                  $query = " SELECT COUNT(`id`) AS `messages` FROM " . T_MESSAGES . " WHERE ((`from_id` = {$user_id} AND `to_id` = {$logged_user_id}) OR (`from_id` = {$logged_user_id} AND `to_id` = {$user_id}))";
            }
      } else {
            $query = " SELECT COUNT(`from_id`) AS `messages` FROM " . T_MESSAGES . " WHERE `to_id` = {$logged_user_id}";
      }
      if (isset($data['new']) && $data['new'] == true) {
            $query .= " AND `seen` = 0";
      }
      $sql_query = mysqli_query($sqlConnect, $query);
      $sql_fetch = mysqli_fetch_assoc($sql_query);
      return $sql_fetch['messages'];
}
function Wo_SeenMessage($message_id) {
      global $sqlConnect;
      $message_id   = Wo_Secure($message_id);
      $query        = mysqli_query($sqlConnect, " SELECT `seen` FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
      $fetched_data = mysqli_fetch_assoc($query);
      if ($fetched_data['seen'] > 0) {
            $data         = array();
            $data['time'] = date('c', $fetched_data['seen']);
            $data['seen'] = Wo_Time_Elapsed_String($fetched_data['seen']);
            return $data;
      } else {
            return false;
      }
}
function Wo_GetMessageButton($user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!is_numeric($user_id) or $user_id < 0) {
            return false;
      }
      if ($user_id == $wo['user']['user_id']) {
            return false;
      }
      $user_id        = Wo_Secure($user_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $message_button = 'buttons/message';
      $account        = $wo['message'] = Wo_UserData($user_id);
      if (!isset($account['user_id'])) {
            return false;
      }
      if ($account['message_privacy'] == 1) {
            if (Wo_IsFollowing($logged_user_id, $user_id) === true) {
                  return Wo_LoadPage($message_button);
            }
      } else if ($account['message_privacy'] == 0) {
            return Wo_LoadPage($message_button);
      }
}
function Wo_Markup($text, $link = true, $hashtag = true, $mention = true) {
      if ($link == true) {
            $link_search = '/\[a\](.*?)\[\/a\]/i';
            if (preg_match_all($link_search, $text, $matches)) {
                  foreach ($matches[1] as $match) {
                        $match_decode = urldecode($match);
                        $match_url    = $match_decode;
                        if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                              $match_url = 'http://' . $match_url;
                        }
                        $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode . '</a>', $text);
                  }
            }
      }
      if ($hashtag == true) {
            $hashtag_regex = '/(#\[([0-9]+)\])/i';
            preg_match_all($hashtag_regex, $text, $matches);
            $match_i = 0;
            foreach ($matches[1] as $match) {
                  $hashtag  = $matches[1][$match_i];
                  $hashkey  = $matches[2][$match_i];
                  $hashdata = Wo_GetHashtag($hashkey);
                  if (is_array($hashdata)) {
                        $hashlink = '<a href="' . Wo_SeoLink('index.php?tab1=hashtag&hash=' . $hashdata['tag']) . '" class="hash">#' . $hashdata['tag'] . '</a>';
                        $text     = str_replace($hashtag, $hashlink, $text);
                  }
                  $match_i++;
            }
      }
      if ($mention == true) {
            $mention_regex = '/@\[([0-9]+)\]/i';
            if (preg_match_all($mention_regex, $text, $matches)) {
                  foreach ($matches[1] as $match) {
                        $match         = Wo_Secure($match);
                        $match_user    = Wo_UserData($match);
                        $match_search  = '@[' . $match . ']';
                        $match_replace = '<span class="user-popover" data-id="' . $match_user['id'] . '" data-type="' . $match_user['type'] . '"><a href="' . Wo_SeoLink('index.php?tab1=timeline&u=' . $match_user['username']) . '" class="hash">' . $match_user['name'] . '</a></span>';
                        if (isset($match_user['user_id'])) {
                              $text = str_replace($match_search, $match_replace, $text);
                        }
                  }
            }
      }
      return $text;
}
function Wo_EditMarkup($text, $link = true, $hashtag = true, $mention = true) {
      if ($link == true) {
            $link_search = '/\[a\](.*?)\[\/a\]/i';
            if (preg_match_all($link_search, $text, $matches)) {
                  foreach ($matches[1] as $match) {
                        $match_decode = urldecode($match);
                        $match_url    = $match_decode;
                        if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                              $match_url = 'http://' . $match_url;
                        }
                        $text = str_replace('[a]' . $match . '[/a]', $match_decode, $text);
                  }
            }
      }
      if ($hashtag == true) {
            $hashtag_regex = '/(#\[([0-9]+)\])/i';
            preg_match_all($hashtag_regex, $text, $matches);
            $match_i = 0;
            foreach ($matches[1] as $match) {
                  $hashtag  = $matches[1][$match_i];
                  $hashkey  = $matches[2][$match_i];
                  $hashdata = Wo_GetHashtag($hashkey);
                  if (is_array($hashdata)) {
                        $hashlink = '#' . $hashdata['tag'];
                        $text     = str_replace($hashtag, $hashlink, $text);
                  }
                  $match_i++;
            }
      }
      if ($mention == true) {
            $mention_regex = '/@\[([0-9]+)\]/i';
            if (preg_match_all($mention_regex, $text, $matches)) {
                  foreach ($matches[1] as $match) {
                        $match         = Wo_Secure($match);
                        $match_user    = Wo_UserData($match);
                        $match_search  = '@[' . $match . ']';
                        $match_replace = '@' . $match_user['username'];
                        if (isset($match_user['user_id'])) {
                              $text = str_replace($match_search, $match_replace, $text);
                        }
                  }
            }
      }
      return $text;
}
function Wo_Emo($string = '') {
      global $emo;
      foreach ($emo as $code => $name) {
            $code   = $code;
            $name   = '<i class="twa-lg twa twa-' . $name . '"></i>';
            $string = str_replace($code, $name, $string);
      }
      return $string;
}
function Wo_UploadLogo($data = array()) {
      global $wo, $sqlConnect;
      if (isset($data['file']) && !empty($data['file'])) {
            $data['file'] = Wo_Secure($data['file']);
      }
      if (isset($data['name']) && !empty($data['name'])) {
            $data['name'] = Wo_Secure($data['name']);
      }
      if (isset($data['name']) && !empty($data['name'])) {
            $data['name'] = Wo_Secure($data['name']);
      }
      if (empty($data)) {
            return false;
      }
      $allowed           = 'jpg,png,jpeg,gif';
      $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
      $extension_allowed = explode(',', $allowed);
      $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
      if (!in_array($file_extension, $extension_allowed)) {
            return false;
      }
      $dir      = "themes/" . $wo['config']['theme'] . "/img/";
      $filename = $dir . "logo.{$file_extension}";
      if (move_uploaded_file($data['file'], $filename)) {
            if (Wo_SaveConfig('logo_extension', $file_extension)) {
                  return true;
            }
      }
}
function Wo_ShareFile($data = array(), $type = 0) {
      global $wo, $sqlConnect;
      $allowed = '';
      if (!file_exists('upload/files/' . date('Y'))) {
            @mkdir('upload/files/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/files/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/files/' . date('Y') . '/' . date('m'), 0777, true);
      }
      if (!file_exists('upload/photos/' . date('Y'))) {
            @mkdir('upload/photos/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
      }
      if (!file_exists('upload/videos/' . date('Y'))) {
            @mkdir('upload/videos/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/videos/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/videos/' . date('Y') . '/' . date('m'), 0777, true);
      }
      if (!file_exists('upload/sounds/' . date('Y'))) {
            @mkdir('upload/sounds/' . date('Y'), 0777, true);
      }
      if (!file_exists('upload/sounds/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/sounds/' . date('Y') . '/' . date('m'), 0777, true);
      }
      if (isset($data['file']) && !empty($data['file'])) {
            $data['file'] = Wo_Secure($data['file']);
      }
      if (isset($data['name']) && !empty($data['name'])) {
            $data['name'] = Wo_Secure($data['name']);
      }
      if (isset($data['name']) && !empty($data['name'])) {
            $data['name'] = Wo_Secure($data['name']);
      }
      if (empty($data)) {
            return false;
      }
      if ($wo['config']['fileSharing'] == 1) {
            if (isset($data['types'])) {
                  $allowed = $data['types'];
            } else {
                  $allowed = $wo['config']['allowedExtenstion'];
            }
      } else {
            $allowed = 'jpg,png,jpeg,gif';
      }
      $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
      $extension_allowed = explode(',', $allowed);
      $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
      if (!in_array($file_extension, $extension_allowed)) {
            return false;
      }
      if ($data['size'] > $wo['config']['maxUpload']) {
            return false;
      }
      if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
            $folder   = 'photos';
            $fileType = 'image';
      } else if ($file_extension == 'mp4' || $file_extension == 'webm' || $file_extension == 'flv') {
            $folder   = 'videos';
            $fileType = 'video';
      } else if ($file_extension == 'mp3' || $file_extension == 'wav') {
            $folder   = 'sounds';
            $fileType = 'soundFile';
      } else {
            $folder   = 'files';
            $fileType = 'file';
      }
      if (empty($folder) || empty($fileType)) {
            return false;
      }
      $dir         = "upload/{$folder}/" . date('Y') . '/' . date('m');
      $filename    = $dir . '/' . Wo_GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
      $second_file = pathinfo($filename, PATHINFO_EXTENSION);
      if (move_uploaded_file($data['file'], $filename)) {
            if ($second_file == 'jpg' || $second_file == 'jpeg' || $second_file == 'png' || $second_file == 'gif') {
                  if ($type == 1) {
                        @Wo_CompressImage($filename, $filename, 80);
                        $explode2  = @end(explode('.', $filename));
                        $explode3  = @explode('.', $filename);
                        $last_file = $explode3[0] . '_small.' . $explode2;
                        @Wo_Resize_Crop_Image(400, 400, $filename, $last_file, 100);
                  } else {
                        @Wo_CompressImage($filename, $filename, 80);
                  }
            }
            $last_data             = array();
            $last_data['filename'] = $filename;
            $last_data['name']     = $data['name'];
            return $last_data;
      }
}
function Wo_DisplaySharedFile($media, $placement = '') {
      global $wo, $sqlConnect;
      $wo['media']['filename'] = Wo_GetMedia($media['filename']);
      $wo['media']['name']     = Wo_Secure($media['name']);
      $wo['media']['type']     = $media['type'];
      $icon_size               = 'fa-2x';
      if ($placement == 'chat') {
            $icon_size = '';
      }
      if (!empty($wo['media']['filename'])) {
            $file_extension = pathinfo($wo['media']['filename'], PATHINFO_EXTENSION);
            $file           = '';
            $media_file     = '';
            $start_link     = "<a href=" . $wo['media']['filename'] . ">";
            $end_link       = '</a>';
            $file_extension = strtolower($file_extension);
            if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
                  if ($placement != 'chat' && $media['type'] != 'message') {
                        $media_file .= "<img src='" . $wo['media']['filename'] . "' alt='image' class='image-file pointer' onclick='Wo_OpenLightBox(" . $media['storyId'] . ");'>";
                  } else {
                        $media_file .= "<a href='" . $wo['media']['filename'] . "' target='_blank'><img src='" . $wo['media']['filename'] . "' alt='image' class='image-file pointer'></a>";
                  }
            }
            if ($file_extension == 'pdf') {
                  $file .= '<i class="fa ' . $icon_size . ' fa-file-pdf-o"></i> ' . $wo['media']['name'];
            }
            if ($file_extension == 'txt') {
                  $file .= '<i class="fa ' . $icon_size . ' fa-file-text-o"></i> ' . $wo['media']['name'];
            }
            if ($file_extension == 'zip' || $file_extension == 'rar' || $file_extension == 'tar') {
                  $file .= '<i class="fa ' . $icon_size . ' fa-file-archive-o"></i> ' . $wo['media']['name'];
            }
            if ($file_extension == 'doc' || $file_extension == 'docx') {
                  $file .= '<i class="fa ' . $icon_size . ' fa-file-word-o"></i> ' . $wo['media']['name'];
            }
            if ($file_extension == 'mp3' || $file_extension == 'wav') {
                  if ($placement == 'chat') {
                        $file .= '<i class="fa ' . $icon_size . ' fa-music"></i> ' . $wo['media']['name'];
                  } else {
                        $media_file .= Wo_LoadPage('players/audio');
                  }
            }
            if (empty($file)) {
                  $file .= '<i class="fa ' . $icon_size . ' fa-file-o"></i> ' . $wo['media']['name'];
            }
            if ($file_extension == 'mp4' || $file_extension == 'mkv') {
                  $media_file .= Wo_LoadPage('players/video');
            }
            $last_file_view = '';
            if (isset($media_file) && !empty($media_file)) {
                  $last_file_view = $media_file;
            } else {
                  $last_file_view = $start_link . $file . $end_link;
            }
            return $last_file_view;
      }
}
function Wo_IsAdmin($user_id) {
      global $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            $user_id = $wo['user']['user_id'];
      }
      $query = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `admin` = '1' AND `user_id` = {$user_id} AND `active` = '1'");
      return (Wo_Sql_Result($query, '0') == 1) ? true : false;
}
function Wo_RegisterPost($re_data = array('recipient_id' => 0)) {
      global $wo, $sqlConnect;
      $is_there_video = false;
      if (empty($re_data['user_id']) or $re_data['user_id'] == 0) {
            $re_data['user_id'] = $wo['user']['user_id'];
      }
      if (!is_numeric($re_data['user_id']) or $re_data['user_id'] < 0) {
            return false;
      }
      if ($re_data['user_id'] == $wo['user']['user_id']) {
            $timeline = $wo['user'];
      } else {
            $re_data['user_id'] = Wo_Secure($re_data['user_id']);
            $timeline           = Wo_UserData($re_data['user_id']);
      }
      if ($timeline['user_id'] != $wo['user']['user_id']) {
            return false;
      }
      if (!empty($re_data['page_id'])) {
            if (Wo_IsPageOnwer($re_data['page_id']) === false) {
                  return false;
            }
      }
      if (!empty($re_data['group_id'])) {
            if (Wo_CanBeOnGroup($re_data['group_id']) === false) {
                  return false;
            }
      }
      if (!empty($re_data['postText'])) {
            if ($wo['config']['maxCharacters'] > 0) {
                  if (mb_strlen($re_data['postText']) > $wo['config']['maxCharacters']) {
                  }
            }
            $re_data['postVine']        = '';
            $re_data['postYoutube']     = '';
            $re_data['postVimeo']       = '';
            $re_data['postDailymotion'] = '';
            $re_data['postFacebook']    = '';
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $re_data['postText'], $match)) {
                  $re_data['postYoutube'] = Wo_Secure($match[1]);
                  $is_there_video         = true;
            }
            if (preg_match("#(?<=vine.co/v/)[0-9A-Za-z]+#", $re_data['postText'], $match)) {
                  $re_data['postVine'] = Wo_Secure($match[0]);
                  $is_there_video      = true;
            }
            if (preg_match("#https?://vimeo.com/([0-9]+)#i", $re_data['postText'], $match)) {
                  $re_data['postVimeo'] = Wo_Secure($match[1]);
                  $is_there_video       = true;
            }
            if (preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $re_data['postText'], $match)) {
                  $re_data['postDailymotion'] = Wo_Secure($match[1]);
                  $is_there_video             = true;
            }
            if (preg_match('~/videos/(?:t\.\d+/)?(\d+)~i', $re_data['postText'], $match)) {
                  $re_data['postFacebook'] = Wo_Secure($match[1]);
                  $is_there_video          = true;
            }
            if (preg_match("~\bfacebook\.com.*?\bv=(\d+)~", $re_data['postText'], $match)) {
                  $is_there_video = true;
            }
            if (preg_match('%(?:https?://)(?:www\.)?soundcloud\.com/([\-a-z0-9_]+/[\-a-z0-9_]+)%im', $re_data['postText'], $match)) {
                  $arrContextOptions = array(
                        "ssl" => array(
                              "verify_peer" => false,
                              "verify_peer_name" => false
                        )
                  );
                  $url               = "https://api.soundcloud.com/resolve.json?url=" . $match[0] . "&client_id=d4f8636b1b1d07e4461dcdc1db226a53";
                  $track_json        = @file_get_contents($url, false, stream_context_create($arrContextOptions));
                  $track             = json_decode($track_json);
                  if (!empty($track->id)) {
                        $re_data['postSoundCloud'] = $track->id;
                  }
            }
            $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
            $i          = 0;
            preg_match_all($link_regex, $re_data['postText'], $matches);
            foreach ($matches[0] as $match) {
                  $match_url           = strip_tags($match);
                  $syntax              = '[a]' . urlencode($match_url) . '[/a]';
                  $re_data['postText'] = str_replace($match, $syntax, $re_data['postText']);
            }
            $mention_regex = '/@([A-Za-z0-9_]+)/i';
            preg_match_all($mention_regex, $re_data['postText'], $matches);
            foreach ($matches[1] as $match) {
                  $match         = Wo_Secure($match);
                  $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
                  $match_search  = '@' . $match;
                  $match_replace = '@[' . $match_user['user_id'] . ']';
                  if (isset($match_user['user_id'])) {
                        $re_data['postText'] = str_replace($match_search, $match_replace, $re_data['postText']);
                        $mentions[]          = $match_user['user_id'];
                  }
            }
            $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
            preg_match_all($hashtag_regex, $re_data['postText'], $matches);
            foreach ($matches[1] as $match) {
                  if (!is_numeric($match)) {
                        $hashdata = Wo_GetHashtag($match);
                        if (is_array($hashdata)) {
                              $match_search        = '#' . $match;
                              $match_replace       = '#[' . $hashdata['id'] . ']';
                              $re_data['postText'] = str_replace($match_search, $match_replace, $re_data['postText']);
                              $hashtag_query       = "UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                              $hashtag_sql_query   = mysqli_query($sqlConnect, $hashtag_query);
                        }
                  }
            }
      }
      $re_data['registered'] = date('n') . '/' . date("Y");
      if ($is_there_video == true) {
            $re_data['postFile'] = '';
      }
      if (!empty($re_data['postVine'])) {
            $re_data['postYoutube']     = '';
            $re_data['postVimeo']       = '';
            $re_data['postDailymotion'] = '';
            $re_data['postFacebook']    = '';
            $re_data['postSoundCloud']  = '';
      }
      if (!empty($re_data['postYoutube'])) {
            $re_data['postVine']        = '';
            $re_data['postVimeo']       = '';
            $re_data['postDailymotion'] = '';
            $re_data['postFacebook']    = '';
            $re_data['postSoundCloud']  = '';
      }
      if (!empty($re_data['postVimeo'])) {
            $re_data['postVine']        = '';
            $re_data['postYoutube']     = '';
            $re_data['postDailymotion'] = '';
            $re_data['postFacebook']    = '';
            $re_data['postSoundCloud']  = '';
      }
      if (!empty($re_data['postDailymotion'])) {
            $re_data['postYoutube']    = '';
            $re_data['postVimeo']      = '';
            $re_data['postVine']       = '';
            $re_data['postFacebook']   = '';
            $re_data['postSoundCloud'] = '';
      }
      if (!empty($re_data['postFacebook'])) {
            $re_data['postYoutube']     = '';
            $re_data['postVimeo']       = '';
            $re_data['postDailymotion'] = '';
            $re_data['postVine']        = '';
            $re_data['postSoundCloud']  = '';
      }
      if (!empty($re_data['postSoundCloud'])) {
            $re_data['postYoutube']     = '';
            $re_data['postVimeo']       = '';
            $re_data['postDailymotion'] = '';
            $re_data['postFacebook']    = '';
            $re_data['postVine']        = '';
      }
      if (empty($re_data['multi_image'])) {
            $re_data['multi_image'] = 0;
      }
      if (empty($re_data['postText']) && empty($re_data['album_name']) && $re_data['multi_image'] == 0 && empty($re_data['postFacebook']) && empty($re_data['postVimeo']) && empty($re_data['postDailymotion']) && empty($re_data['postVine']) && empty($re_data['postYoutube']) && empty($re_data['postFile']) && empty($re_data['postSoundCloud']) && empty($re_data['postFeeling']) && empty($re_data['postListening']) && empty($re_data['postPlaying']) && empty($re_data['postWatching']) && empty($re_data['postTraveling']) && empty($re_data['postMap'])) {
            return false;
      }
      if (!empty($re_data['recipient_id']) && is_numeric($re_data['recipient_id']) && $re_data['recipient_id'] > 0) {
            if ($re_data['recipient_id'] == $re_data['user_id']) {
                  return false;
            }
            $recipient = Wo_UserData($re_data['recipient_id']);
            if (empty($recipient['user_id'])) {
                  return false;
            }
            if (!empty($recipient['user_id'])) {
                  if ($recipient['post_privacy'] == 'ifollow') {
                        if (Wo_IsFollowing($recipient['user_id'], $wo['user']['user_id']) === false) {
                              return false;
                        }
                  } else if ($recipient['post_privacy'] == 'nobody') {
                        return false;
                  }
            }
      }
      if (!isset($re_data['postType'])) {
            $re_data['postType'] = 'post';
      }
      if (!empty($re_data['page_id'])) {
            $re_data['user_id'] = 0;
      }
      $fields  = '`' . implode('`, `', array_keys($re_data)) . '`';
      $data    = '\'' . implode('\', \'', $re_data) . '\'';
      $query   = mysqli_query($sqlConnect, "INSERT INTO " . T_POSTS . " ({$fields}) VALUES ({$data})");
      $post_id = mysqli_insert_id($sqlConnect);
      if ($query) {
            mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `post_id` = {$post_id} WHERE `id` = {$post_id}");
            if (isset($recipient['user_id'])) {
                  $notification_data_array = array(
                        'recipient_id' => $recipient['user_id'],
                        'post_id' => $post_id,
                        'type' => 'profile_wall_post',
                        'url' => 'index.php?tab1=post&id=' . $post_id
                  );
                  Wo_RegisterNotification($notification_data_array);
            }
            if (isset($mentions) && is_array($mentions)) {
                  foreach ($mentions as $mention) {
                        $notification_data_array = array(
                              'recipient_id' => $mention,
                              'page_id' => $re_data['page_id'],
                              'type' => 'post_mention',
                              'url' => 'index.php?tab1=post&id=' . $post_id
                        );
                        Wo_RegisterNotification($notification_data_array);
                  }
            }
            return $post_id;
      }
}
function Wo_GetHashtag($tag = '') {
      global $sqlConnect;
      $create = false;
      if (empty($tag)) {
            return false;
      }
      $tag = Wo_Secure($tag);
      if (is_numeric($tag)) {
            $query = " SELECT * FROM " . T_HASHTAGS . " WHERE `id` = {$tag}";
      } else {
            $query  = " SELECT * FROM " . T_HASHTAGS . " WHERE `tag` = '{$tag}' ";
            $create = true;
      }
      $sql_query   = mysqli_query($sqlConnect, $query);
      $sql_numrows = mysqli_num_rows($sql_query);
      if ($sql_numrows == 1) {
            $sql_fetch = mysqli_fetch_assoc($sql_query);
            return $sql_fetch;
      } elseif ($sql_numrows == 0) {
            if ($create == true) {
                  $hash          = md5($tag);
                  $query_two     = " INSERT INTO " . T_HASHTAGS . " (`hash`, `tag`, `last_trend_time`) VALUES ('{$hash}', '{$tag}', " . time() . ")";
                  $sql_query_two = mysqli_query($sqlConnect, $query_two);
                  if ($sql_query_two) {
                        $sql_id = mysqli_insert_id($sqlConnect);
                        $get    = array(
                              'id' => $sql_id,
                              'hash' => $hash,
                              'tag' => $tag,
                              'last_trend_time' => time(),
                              'trend_use_num' => 0
                        );
                        return $get;
                  }
            }
      }
}
function Wo_PostData($post_id, $placement = '', $limited = '') {
      global $wo, $sqlConnect, $cache;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      $data           = array();
      $post_id        = Wo_Secure($post_id);
      $query_one      = "SELECT * FROM " . T_POSTS . " WHERE `id` = {$post_id}";
      $hashed_post_Id = md5($post_id);
      if ($wo['config']['cacheSystem'] == 1) {
            $fetched_data = $cache->read($hashed_post_Id . '_P_Data.tmp');
            if (empty($fetched_data)) {
                  $sql_query_one = mysqli_query($sqlConnect, $query_one);
                  $fetched_data  = mysqli_fetch_assoc($sql_query_one);
                  $cache->write($hashed_post_Id . '_P_Data.tmp', $fetched_data);
            }
      } else {
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            $fetched_data  = mysqli_fetch_assoc($sql_query_one);
      }
      if (!empty($fetched_data['page_id'])) {
            $fetched_data['publisher'] = Wo_PageData($fetched_data['page_id']);
      } else {
            $fetched_data['publisher'] = Wo_UserData($fetched_data['user_id']);
      }
      if ($fetched_data['id'] == $fetched_data['post_id']) {
            $story = $fetched_data;
      } else {
            $query_two     = "SELECT * FROM " . T_POSTS . " WHERE `id` = " . $fetched_data['post_id'];
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if (mysqli_num_rows($sql_query_two) != 1) {
                  return false;
            }
            $sql_fetch_two = mysqli_fetch_assoc($sql_query_two);
            $story         = $sql_fetch_two;
            if (!empty($story['page_id'])) {
                  $story['publisher'] = Wo_PageData($story['page_id']);
            } else {
                  $story['publisher'] = Wo_UserData($story['user_id']);
            }
      }
      $story['limit_comments']   = 5;
      $story['limited_comments'] = true;
      if ($limited == 'not_limited') {
            $story['limit_comments']   = 10000;
            $story['limited_comments'] = false;
      }
      if ($placement != 'admin') {
            if ($story['postPrivacy'] == 1) {
                  if (Wo_IsLogged() === true) {
                        if (!empty($story['publisher']['page_id'])) {
                        } else {
                              if ($story['publisher']['user_id'] != $wo['user']['user_id']) {
                                    if (Wo_IsFollowing($wo['user']['user_id'], $story['publisher']['user_id']) === false) {
                                          return false;
                                    }
                              }
                        }
                  } else {
                        return false;
                  }
            }
            if ($story['postPrivacy'] == 2) {
                  if (Wo_IsLogged() === true) {
                        if (!empty($story['publisher']['page_id'])) {
                              if ($story['publisher']['user_id'] != $wo['user']['user_id']) {
                                    if (Wo_IsPageLiked($story['publisher']['page_id'], $wo['user']['user_id']) === false) {
                                          return false;
                                    }
                              }
                        } else {
                              if ($story['publisher']['user_id'] != $wo['user']['user_id']) {
                                    if (Wo_IsFollowing($story['publisher']['user_id'], $wo['user']['user_id']) === false) {
                                          return false;
                                    }
                              }
                        }
                  } else {
                        return false;
                  }
            }
            if ($story['postPrivacy'] == 3) {
                  if (Wo_IsLogged() === true) {
                        if (!empty($story['publisher']['page_id'])) {
                        } else {
                              if ($wo['user']['user_id'] != $story['publisher']['user_id']) {
                                    return false;
                              }
                        }
                  } else {
                        return false;
                  }
            }
      }
      $story['Orginaltext']            = Wo_EditMarkup($story['postText']);
      $story['Orginaltext']            = str_replace('<br>', "\n", $story['Orginaltext']);
      $story['postText']               = Wo_Emo($story['postText']);
      $story['postText']               = Wo_Markup($story['postText']);
      $story['page']                   = 0;
      $story['is_group_post']          = false;
      $story['group_recipient_exists'] = false;
      if (!empty($story['group_id'])) {
            $story['group_recipient_exists'] = true;
            $story['group_recipient']        = Wo_GroupData($story['group_id']);
            if ($story['group_recipient']['privacy'] == 1) {
                  if (Wo_IsGroupOnwer($story['group_id']) === false) {
                        if (Wo_IsGroupJoined($story['group_id']) === false) {
                              return false;
                        }
                  }
            }
            if (Wo_IsGroupOnwer($story['group_id']) === false) {
                  $story['is_group_post'] = true;
            }
      }
      if (!empty($story['postFeeling'])) {
            $story['postFeelingIcon'] = $wo['feelingIcons'][$story['postFeeling']];
      }
      if (isset($story['Orginaltext']) && !empty($story['Orginaltext']) && $wo['config']['useSeoFrindly'] == 1) {
            $story['url'] = Wo_SeoLink('index.php?tab1=post&id=' . $story['id']) . '_' . Wo_SlugPost($story['Orginaltext']);
      } else {
            $story['url'] = Wo_SeoLink('index.php?tab1=post&id=' . $story['id']);
      }
      $story['via_type'] = '';
      if ($story['id'] != $fetched_data['id'] && $story['user_id'] != $fetched_data['user_id']) {
            $story['via_type'] = 'share';
            $story['via']      = $fetched_data['publisher'];
      }
      $story['recipient_exists'] = false;
      $story['recipient']        = '';
      if ($story['recipient_id'] > 0) {
            $story['recipient_exists'] = true;
            $story['recipient']        = Wo_UserData($story['recipient_id']);
      }
      $story['admin'] = false;
      if (Wo_IsLogged() === true) {
            if (!empty($story['page_id'])) {
                  if (Wo_IsPageOnwer($story['page_id'])) {
                        $story['admin'] = true;
                  }
            } else {
                  if ($story['publisher']['user_id'] == $wo['user']['user_id']) {
                        $story['admin'] = true;
                  }
            }
            if ($story['recipient_exists'] == true) {
                  if ($story['recipient']['user_id'] == $wo['user']['user_id']) {
                        $story['admin'] = true;
                  }
            }
      }
      $story['is_post_saved']     = false;
      $story['is_post_reported']  = false;
      $story['is_liked']          = false;
      $story['is_wondered']       = false;
      $story['post_comments']     = 0;
      $story['post_shares']       = 0;
      $story['post_likes']        = 0;
      $story['post_wonders']      = 0;
      $story['postLinkImage']     = Wo_GetMedia($story['postLinkImage']);
      $story['is_post_pinned']    = (Wo_IsPostPinned($story['id']) === true) ? true : false;
      $story['get_post_comments'] = Wo_GetPostComments($story['id'], $story['limit_comments']);
      $story['photo_album']       = array();
      if (!empty($story['album_name'])) {
            $story['photo_album'] = Wo_GetAlbumPhotos($story['id']);
      }
      if ($story['multi_image'] == 1) {
            $story['photo_multi'] = Wo_GetAlbumPhotos($story['id']);
      }
      if (Wo_IsLogged() === true) {
            $story['post_comments']    = Wo_CountPostComment($story['id']);
            $story['post_shares']      = Wo_CountShares($story['id']);
            $story['post_likes']       = Wo_CountLikes($story['id']);
            $story['post_wonders']     = Wo_CountWonders($story['id']);
            $story['is_liked']         = (Wo_IsLiked($story['id'], $wo['user']['user_id']) === true) ? true : false;
            $story['is_wondered']      = (Wo_IsWondered($story['id'], $wo['user']['user_id']) === true) ? true : false;
            $story['is_post_saved']    = (Wo_IsPostSaved($story['id'], $wo['user']['user_id']) === true) ? true : false;
            $story['is_post_reported'] = (Wo_IsPostRepotred($story['id'], $wo['user']['user_id']) === true) ? true : false;
      }
      return $story;
}
function Wo_CountUserPosts($user_id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_POSTS . " WHERE `user_id` = {$user_id}");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_PostExists($post_id) {
      global $sqlConnect;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      $post_id = Wo_Secure($post_id);
      $query   = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_POSTS . " WHERE `id` = {$post_id}");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_IsPostOnwer($post_id, $user_id) {
      global $sqlConnect;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      $post_id = Wo_Secure($post_id);
      $user_id = Wo_Secure($user_id);
      $query   = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_POSTS . " WHERE `id` = {$post_id} AND (`user_id` = {$user_id} OR `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id}))");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_GetPostPublisherBox($user_id = 0, $recipient_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $continue = true;
      if (empty($user_id) or $user_id == 0) {
            $user_id = $wo['user']['user_id'];
      }
      if (!is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      if ($user_id == $wo['user']['user_id']) {
            $user_timline = $wo['user'];
      } else {
            $user_id      = Wo_Secure($user_id);
            $user_timline = Wo_UserData($user_id);
      }
      if (!isset($recipient_id) or empty($recipient_id)) {
            $recipient_id = 0;
      }
      if (!is_numeric($recipient_id) or $recipient_id < 0) {
            return false;
      }
      $recipient_id = Wo_Secure($recipient_id);
      if ($user_id == $recipient_id) {
            $recipient_id = 0;
      }
      if ($recipient_id > 0) {
            $recipient = Wo_UserData($recipient_id);
            if (!isset($recipient['user_id'])) {
                  return false;
            }
            if ($recipient['post_privacy'] == "ifollow") {
                  if (Wo_IsFollowing($wo['user']['user_id'], $recipient_id) === false) {
                        $continue = false;
                  }
            } elseif ($recipient['post_privacy'] == "nobody") {
                  $continue = false;
            } elseif ($recipient['post_privacy'] == "everyone") {
                  $continue = true;
            }
            $wo['input']['recipient'] = $recipient;
      }
      if ($continue == true) {
            $wo['input']['user_timline'] = $user_timline;
            return Wo_LoadPage('story/publisher-box');
      }
}
function Wo_GetPosts($data = array('filter_by' => 'all', 'after_post_id' => 0, 'page_id' => 0, 'group_id' => 0, 'publisher_id' => 0, 'limit' => 5)) {
      global $wo, $sqlConnect;
      if (empty($data['filter_by'])) {
            $data['filter_by'] = 'all';
      }
      $subquery_one = " `id` > 0 ";
      if (!empty($data['after_post_id']) && is_numeric($data['after_post_id']) && $data['after_post_id'] > 0) {
            $data['after_post_id'] = Wo_Secure($data['after_post_id']);
            $subquery_one          = " `id` < " . $data['after_post_id'] . " AND `id` <> " . $data['after_post_id'];
      } else if (!empty($data['before_post_id']) && is_numeric($data['before_post_id']) && $data['before_post_id'] > 0) {
            $data['before_post_id'] = Wo_Secure($data['before_post_id']);
            $subquery_one           = " `id` > " . $data['before_post_id'] . " AND `id` <> " . $data['before_post_id'];
      }
      if (!empty($data['publisher_id']) && is_numeric($data['publisher_id']) && $data['publisher_id'] > 0) {
            $data['publisher_id'] = Wo_Secure($data['publisher_id']);
            $Wo_publisher         = Wo_UserData($data['publisher_id']);
      }
      if (!empty($data['page_id']) && is_numeric($data['page_id']) && $data['page_id'] > 0) {
            $data['page_id']   = Wo_Secure($data['page_id']);
            $Wo_page_publisher = Wo_PageData($data['page_id']);
      }
      if (!empty($data['group_id']) && is_numeric($data['group_id']) && $data['group_id'] > 0) {
            $data['group_id']   = Wo_Secure($data['group_id']);
            $Wo_group_publisher = Wo_GroupData($data['group_id']);
      }
      $query_text = "SELECT `id` FROM " . T_POSTS . " WHERE {$subquery_one} AND `postType` <> 'profile_picture_deleted'";
      if (isset($Wo_publisher['user_id'])) {
            $user_id = Wo_Secure($Wo_publisher['user_id']);
            $query_text .= " AND (`user_id` = {$user_id} OR `recipient_id` = {$user_id}) AND postShare IN (0,1) AND `id` NOT IN (SELECT `post_id` from " . T_PINNED_POSTS . " WHERE `user_id` = {$user_id})  AND `page_id` NOT IN (SELECT `page_id` from " . T_PAGES . " WHERE user_id = {$user_id}) AND `group_id` = 0";
            switch ($data['filter_by']) {
                  case 'text':
                        $query_text .= " AND `postText` <> '' AND `postFile` = '' AND `postYoutube` = '' AND `postFacebook` = ''  AND `postVimeo` = ''  AND `postDailymotion` = '' AND `postSoundCloud` = '' ";
                        break;
                  case 'files':
                        $query_text .= " AND (`postFile` LIKE '%_file%' AND `postFile` NOT LIKE '%_video%' AND `postFile` NOT LIKE '%_avatar%' AND `postFile` NOT LIKE '%_soundFile%' AND `postFile` NOT LIKE '%_image%')";
                        break;
                  case 'photos':
                        $query_text .= " AND (`postFile` LIKE '%_image%' OR `postFile` LIKE '%_avatar%' OR `postFile` LIKE '%_cover%')";
                        break;
                  case 'music':
                        $query_text .= " AND (`postSoundCloud` <> '' OR `postFile` LIKE '%_soundFile%')";
                        break;
                  case 'video':
                        $query_text .= " AND (`postYoutube` <> '' OR `postVine` <> '' OR `postFacebook` <> '' OR `postDailymotion` <> '' OR `postVimeo` <> '' OR `postFile` LIKE '%_video%')";
                        break;
                  case 'maps':
                        $query_text .= " AND `postMap` <> ''";
                        break;
            }
      } else if (isset($Wo_page_publisher['page_id'])) {
            $page_id = Wo_Secure($Wo_page_publisher['page_id']);
            $query_text .= " AND (`page_id` = {$page_id}) AND `id` NOT IN (SELECT `post_id` from " . T_PINNED_POSTS . " WHERE `page_id` = {$page_id})";
            switch ($data['filter_by']) {
                  case 'text':
                        $query_text .= " AND `postText` <> '' AND `postFile` = '' AND `postYoutube` = '' AND `postFacebook` = ''  AND `postVimeo` = ''  AND `postDailymotion` = '' AND `postSoundCloud` = '' ";
                        break;
                  case 'files':
                        $query_text .= " AND (`postFile` LIKE '%_file%' AND `postFile` NOT LIKE '%_video%' AND `postFile` NOT LIKE '%_avatar%' AND `postFile` NOT LIKE '%_soundFile%' AND `postFile` NOT LIKE '%_image%')";
                        break;
                  case 'photos':
                        $query_text .= " AND (`postFile` LIKE '%_image%' OR `postFile` LIKE '%_avatar%')";
                        break;
                  case 'music':
                        $query_text .= " AND (`postSoundCloud` <> '' OR `postFile` LIKE '%_soundFile%')";
                        break;
                  case 'video':
                        $query_text .= " AND (`postYoutube` <> '' OR `postVine` <> '' OR `postFacebook` <> '' OR `postDailymotion` <> '' OR `postVimeo` <> '' OR `postFile` LIKE '%_video%')";
                        break;
                  case 'maps':
                        $query_text .= " AND `postMap` <> ''";
                        break;
            }
      } else if (isset($Wo_group_publisher['id'])) {
            $group_id = Wo_Secure($Wo_group_publisher['id']);
            $query_text .= " AND (`group_id` = {$group_id}) AND `id` NOT IN (SELECT `post_id` from " . T_PINNED_POSTS . " WHERE `group_id` = {$group_id})";
            switch ($data['filter_by']) {
                  case 'text':
                        $query_text .= " AND `postText` <> '' AND `postFile` = '' AND `postYoutube` = '' AND `postFacebook` = ''  AND `postVimeo` = ''  AND `postDailymotion` = '' AND `postSoundCloud` = '' ";
                        break;
                  case 'files':
                        $query_text .= " AND (`postFile` LIKE '%_file%' AND `postFile` NOT LIKE '%_video%' AND `postFile` NOT LIKE '%_avatar%' AND `postFile` NOT LIKE '%_soundFile%' AND `postFile` NOT LIKE '%_image%')";
                        break;
                  case 'photos':
                        $query_text .= " AND (`postFile` LIKE '%_image%' OR `postFile` LIKE '%_avatar%')";
                        break;
                  case 'music':
                        $query_text .= " AND (`postSoundCloud` <> '' OR `postFile` LIKE '%_soundFile%')";
                        break;
                  case 'video':
                        $query_text .= " AND (`postYoutube` <> '' OR `postVine` <> '' OR `postFacebook` <> '' OR `postDailymotion` <> '' OR `postVimeo` <> '' OR `postFile` LIKE '%_video%')";
                        break;
                  case 'maps':
                        $query_text .= " AND `postMap` <> ''";
                        break;
            }
      } else {
            $query_text .= " AND (`user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = " . $wo['user']['user_id'] . " AND `active` = '1') OR `recipient_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = " . Wo_Secure($wo['user']['user_id']) . " AND `active` = '1' ) OR `user_id` IN (" . Wo_Secure($wo['user']['user_id']) . ") OR `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = " . $wo['user']['user_id'] . " AND `active` = '1') OR `page_id` IN (SELECT `page_id` FROM " . T_PAGES_LIKES . " WHERE `user_id` = " . $wo['user']['user_id'] . " AND `active` = '1') OR `group_id` IN (SELECT id FROM " . T_GROUPS . " WHERE `user_id` = " . $wo['user']['user_id'] . ") OR `group_id` IN(SELECT `group_id` FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = " . $wo['user']['user_id'] . ")) AND `postShare` NOT IN (1)";
            switch ($data['filter_by']) {
                  case 'text':
                        $query_text .= " AND `postText` <> '' AND `postFile` = '' AND `postYoutube` = '' AND `postFacebook` = ''  AND `postVimeo` = ''  AND `postDailymotion` = '' AND `postSoundCloud` = '' ";
                        break;
                  case 'files':
                        $query_text .= " AND (`postFile` LIKE '%_file%' AND `postFile` NOT LIKE '%_video%' AND `postFile` NOT LIKE '%_avatar%' AND `postFile` NOT LIKE '%_soundFile%' AND `postFile` NOT LIKE '%_image%')";
                        break;
                  case 'photos':
                        $query_text .= " AND (`postFile` LIKE '%_image%' OR `postFile` LIKE '%_avatar%')";
                        break;
                  case 'music':
                        $query_text .= " AND (`postSoundCloud` <> '' OR `postFile` LIKE '%_soundFile%')";
                        break;
                  case 'video':
                        $query_text .= " AND (`postYoutube` <> '' OR `postVine` <> '' OR `postFacebook` <> '' OR `postDailymotion` <> '' OR `postVimeo` <> '' OR `postFile` LIKE '%_video%')";
                        break;
                  case 'maps':
                        $query_text .= " AND `postMap` <> ''";
                        break;
            }
      }
      if (empty($data['limit']) or !is_numeric($data['limit']) or $data['limit'] < 1) {
            $data['limit'] = 5;
      }
      $limit = Wo_Secure($data['limit']);
      if (isset($data['order'])) {
            $query_text .= " ORDER BY `id` " . Wo_Secure($data['order']) . " LIMIT {$limit}";
      } else {
            $query_text .= " ORDER BY `id` DESC LIMIT {$limit}";
      }
      $data = array();
      $sql  = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $post = Wo_PostData($fetched_data['id']);
            if (is_array($post)) {
                  $data[] = $post;
            }
      }
      return $data;
}
function Wo_DeletePost($post_id = 0) {
      global $wo, $sqlConnect, $cache;
      if ($post_id < 1 || empty($post_id) || !is_numeric($post_id)) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $post_id = Wo_Secure($post_id);
      $query   = mysqli_query($sqlConnect, "SELECT `id`, `user_id`, `recipient_id`, `page_id`, `postFile`, `postType`, `postLinkImage`, `multi_image`, `album_name` FROM " . T_POSTS . " WHERE `id` = {$post_id} AND (`user_id` = {$user_id} OR `recipient_id` = {$user_id} OR `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id}))");
      if (mysqli_num_rows($query) > 0 || Wo_IsAdmin($wo['user']['user_id']) === true) {
            $fetched_data = mysqli_fetch_assoc($query);
            if ($fetched_data['postType'] == 'profile_picture' || $fetched_data['postType'] == 'profile_picture_deleted' || $fetched_data['postType'] == 'profile_cover_picture') {
                  $query_delete_3 = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `postType` = 'profile_picture_deleted' WHERE `id` = '" . $fetched_data['id'] . "'");
                  return true;
            }
            if (isset($fetched_data['postFile']) && !empty($fetched_data['postFile'])) {
                  if ($fetched_data['postType'] != 'profile_picture' && $fetched_data['postType'] != 'profile_cover_picture') {
                        @unlink(trim($fetched_data['postFile']));
                  }
            }
            if (isset($fetched_data['postLinkImage']) && !empty($fetched_data['postLinkImage'])) {
                  @unlink($fetched_data['postLinkImage']);
            }
            if ($wo['config']['cacheSystem'] == 1) {
                  $cache->delete(md5($post_id) . '_P_Data.tmp');
            }
            if (!empty($fetched_data['album_name']) || !empty($fetched_data['multi_image'])) {
                  $query_delete_4 = mysqli_query($sqlConnect, "SELECT `image` FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id}");
                  while ($fetched_delete_data = mysqli_fetch_assoc($query_delete_4)) {
                        $explode2 = @end(explode('.', $fetched_delete_data['image']));
                        $explode3 = @explode('.', $fetched_delete_data['image']);
                        $media_2  = $explode3[0] . '_small.' . $explode2;
                        @unlink(trim($media_2));
                        @unlink($fetched_delete_data['image']);
                  }
            }
            $query_two_2 = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_COMMENTS . " WHERE `post_id` = {$post_id}");
            while ($fetched_data = mysqli_fetch_assoc($query_two_2)) {
                  Wo_DeletePostComment($fetched_data['id']);
            }
            $query_delete  = mysqli_query($sqlConnect, "DELETE FROM " . T_POSTS . " WHERE `id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_POSTS . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_WONDERS . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_LIKES . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_SAVED_POSTS . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_REPORTS . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_PINNED_POSTS . " WHERE `post_id` = {$post_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_ACTIVITIES . " WHERE `post_id` = {$post_id}");
            return true;
      } else {
            return false;
      }
}
function Wo_DeleteGame($game_id) {
      global $wo, $sqlConnect, $cache;
      if ($game_id < 1 || empty($game_id) || !is_numeric($game_id)) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }

      $user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      $game_id = Wo_Secure($game_id);
      $query_delete  = mysqli_query($sqlConnect, "DELETE FROM " . T_GAMES . " WHERE `id` = {$game_id}");
      $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_GAMES_PLAYERS . " WHERE `game_id` = {$game_id}");
      if ($query_delete) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetUserIdFromPostId($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `user_id` FROM " . T_POSTS . " WHERE `id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['user_id'];
      }
}
function Wo_GetPinnedPost($user_id, $type = '') {
      global $sqlConnect, $wo;
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      $query_type = 'user_id';
      if ($type == 'page') {
            $query_type = 'page_id';
      } else if ($type == 'profile') {
            $query_type = 'user_id';
      } else if ($type == 'group') {
            $query_type = 'group_id';
      }
      $data      = array();
      $query_one = mysqli_query($sqlConnect, "SELECT `post_id` FROM " . T_PINNED_POSTS . " WHERE `{$query_type}` = {$user_id} AND `active` = '1'");
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $post = Wo_PostData($fetched_data['post_id']);
            if (is_array($post)) {
                  $data[] = $post;
            }
      }
      return $data;
}
function Wo_IsPostPinned($post_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as `pinned` FROM " . T_PINNED_POSTS . " WHERE `post_id` = {$post_id} AND `active` = '1'");
      $sql_query_one = mysqli_fetch_assoc($query_one);
      return ($sql_query_one['pinned'] == 1) ? true : false;
}
function Wo_IsUserPinned($id, $type = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id         = Wo_Secure($id);
      $query_type = 'user_id';
      if ($type == 'page') {
            $query_type = 'page_id';
      } else if ($type == 'profile') {
            $query_type = 'user_id';
      } else if ($type == 'group') {
            $query_type = 'group_id';
      }
      $query_one     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as `pinned` FROM " . T_PINNED_POSTS . " WHERE `{$query_type}` = {$id} AND `active` = '1'");
      $sql_query_one = mysqli_fetch_assoc($query_one);
      return ($sql_query_one['pinned'] == 1) ? true : false;
}
function Wo_PinPost($post_id = 0, $type = '', $id = 0) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id  = Wo_Secure($wo['user']['user_id']);
      $post_id  = Wo_Secure($post_id);
      $continue = false;
      if (empty($type)) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      if (Wo_PostExists($post_id) === false) {
            return false;
      }
      if (Wo_IsPostOnwer($post_id, $user_id) === false) {
            return false;
      }
      if ($type == 'page') {
            if (Wo_IsPageOnwer($id) === false) {
                  return false;
            }
            $where_delete_query = " WHERE `page_id` = {$id} AND `active` = '1'";
            $where_insert_query = " (`page_id`, `post_id`, `active`) VALUES ({$id}, {$post_id}, '1')";
      } else if ($type == 'group') {
            if (Wo_IsGroupOnwer($id) === false) {
                  return false;
            }
            $where_delete_query = " WHERE `group_id` = {$id} AND `active` = '1'";
            $where_insert_query = " (`group_id`, `post_id`, `active`) VALUES ({$id}, {$post_id}, '1')";
      } else if ($type == 'profile') {
            $where_delete_query = " WHERE `user_id` = {$user_id} AND `active` = '1'";
            $where_insert_query = " (`user_id`, `post_id`, `active`) VALUES ({$user_id}, {$post_id}, '1')";
      }
      $delete_query_text = "DELETE FROM " . T_PINNED_POSTS;
      $query_text        = $delete_query_text . $where_delete_query;
      $insert_query_text = "INSERT INTO " . T_PINNED_POSTS;
      $insert_text       = $insert_query_text . $where_insert_query;
      if (Wo_IsPostPinned($post_id)) {
            $query_two = mysqli_query($sqlConnect, $query_text);
            return 'unpin';
      } else {
            if (Wo_IsUserPinned($id, $type)) {
                  $query_two = mysqli_query($sqlConnect, $query_text);
                  $continue  = true;
            } else {
                  $continue = true;
            }
            if ($continue === true) {
                  $query_three = mysqli_query($sqlConnect, $insert_text);
                  if ($query_three) {
                        return 'pin';
                  }
            }
      }
}
function Wo_RegisterActivity($data = array()) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if ($wo['user']['show_activities_privacy'] == 0) {
            return false;
      }
      if (empty($data['post_id']) || !is_numeric($data['post_id']) || $data['post_id'] < 1) {
            return false;
      }
      if (empty($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] < 1) {
            return false;
      }
      if (empty($data['activity_type'])) {
            return false;
      }
      $post_id       = Wo_Secure($data['post_id']);
      $user_id       = Wo_Secure($data['user_id']);
      $post_user_id  = Wo_Secure($data['post_user_id']);
      $activity_type = Wo_Secure($data['activity_type']);
      $time          = time();
      if ($user_id == $post_user_id) {
            return false;
      }
      $query_insert = "INSERT INTO " . T_ACTIVITIES . " (`user_id`, `post_id`, `activity_type`, `time`) VALUES ('{$user_id}', '{$post_id}', '{$activity_type}', '{$time}')";
      if (Wo_IsActivity($post_id, $user_id, $activity_type) === true) {
            $query_delete = mysqli_query($sqlConnect, "DELETE FROM " . T_ACTIVITIES . " WHERE `user_id` = '{$user_id}' AND `post_id` = '{$post_id}'");
            if ($query_delete) {
                  $query_one = mysqli_query($sqlConnect, $query_insert);
            }
      } else {
            $query_one = mysqli_query($sqlConnect, $query_insert);
      }
      if ($query_one) {
            return true;
      }
}
function Wo_IsActivity($post_id, $user_id, $activity_type) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_ACTIVITIES . " WHERE `user_id` = '{$user_id}' AND `post_id` = '{$post_id}' AND `activity_type` = '{$activity_type}'");
      return (mysqli_num_rows($query) > 0) ? true : false;
}
function Wo_DeleteActivity($post_id, $user_id, $activity_type) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "DELETE FROM " . T_ACTIVITIES . " WHERE `user_id` = '{$user_id}' AND `post_id` = '{$post_id}' AND `activity_type` = '{$activity_type}'");
      return ($query) ? true : false;
}
function Wo_GetActivity($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (empty($id) || !is_numeric($id) || $id < 1) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_ACTIVITIES . " WHERE `id` = {$id}");
      if (mysqli_num_rows($query) == 1) {
            $finel_fetched_data              = mysqli_fetch_assoc($query);
            $finel_fetched_data['postData']  = Wo_PostData($finel_fetched_data['post_id']);
            $finel_fetched_data['activator'] = Wo_UserData($finel_fetched_data['user_id']);
            return $finel_fetched_data;
      }
}
function Wo_GetActivities($data = array('after_activity_id' => 0, 'before_activity_id' => 0, 'limit' => 5)) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $get     = array();
      if (empty($data['limit'])) {
            $data['limit'] = 5;
      }
      $limit        = Wo_Secure($data['limit']);
      $subquery_one = " `id` > 0 ";
      if (!empty($data['after_activity_id']) && is_numeric($data['after_activity_id']) && $data['after_activity_id'] > 0) {
            $data['after_activity_id'] = Wo_Secure($data['after_activity_id']);
            $subquery_one              = " `id` < " . $data['after_activity_id'] . " AND `id` <> " . $data['after_activity_id'];
      } else if (!empty($data['before_activity_id']) && is_numeric($data['before_activity_id']) && $data['before_activity_id'] > 0) {
            $data['before_activity_id'] = Wo_Secure($data['before_activity_id']);
            $subquery_one               = " `id` > " . $data['before_activity_id'] . " AND `id` <> " . $data['before_activity_id'];
      }
      $query_text = "SELECT `id` FROM " . T_ACTIVITIES . " WHERE {$subquery_one}";
      $query_text .= " AND `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `active` = '1') AND `user_id` NOT IN ($user_id) ORDER BY `id` DESC LIMIT {$limit}";
      $sql_query_one = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            if (is_array($fetched_data)) {
                  $get[] = Wo_GetActivity($fetched_data['id']);
            }
      }
      return $get;
}
function Wo_AddLikes($post_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      $post_id        = Wo_Secure($post_id);
      $user_id        = Wo_GetUserIdFromPostId($post_id);
      $page_id        = 0;
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $post           = Wo_PostData($post_id);
      $text           = '';
      $type2          = '';
      if (empty($user_id)) {
            $user_id = Wo_GetUserIdFromPageId($post['page_id']);
            if (empty($user_id)) {
                  return false;
            }
      }
      if (isset($post['postText']) && !empty($post['postText'])) {
            $text = substr($post['postText'], 0, 10) . '..';
      }
      if (isset($post['postYoutube']) && !empty($post['postYoutube'])) {
            $type2 = 'post_youtube';
      } elseif (isset($post['postSoundCloud']) && !empty($post['postSoundCloud'])) {
            $type2 = 'post_soundcloud';
      } elseif (isset($post['postVine']) && !empty($post['postVine'])) {
            $type2 = 'post_vine';
      } elseif (isset($post['postFile']) && !empty($post['postFile'])) {
            if (strpos($post['postFile'], '_image') !== false) {
                  $type2 = 'post_image';
            } else if (strpos($post['postFile'], '_video') !== false) {
                  $type2 = 'post_video';
            } else if (strpos($post['postFile'], '_avatar') !== false) {
                  $type2 = 'post_avatar';
            } else if (strpos($post['postFile'], '_sound') !== false) {
                  $type2 = 'post_soundFile';
            } else if (strpos($post['postFile'], '_cover') !== false) {
                  $type2 = 'post_cover';
            } else if (strpos($post['postFile'], '_cover') !== false) {
                  $type2 = 'post_cover';
            } else {
                  $type2 = 'post_file';
            }
      }
      if (Wo_IsLiked($post_id, $wo['user']['user_id']) === true) {
            $query_one        = "DELETE FROM " . T_LIKES . " WHERE `post_id` = {$post_id} AND `user_id` = {$logged_user_id}";
            $query_delete_one = mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$user_id} AND `type` = 'liked_post'");
            $delete_activity  = Wo_DeleteActivity($post_id, $logged_user_id, 'liked_post');
            $sql_query_one    = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unliked';
            }
      } else {
            $query_two     = "INSERT INTO " . T_LIKES . " (`user_id`, `post_id`) VALUES ({$logged_user_id}, {$post_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  if ($type2 != 'post_avatar') {
                        $activity_data = array(
                              'post_id' => $post_id,
                              'user_id' => $logged_user_id,
                              'post_user_id' => $user_id,
                              'activity_type' => 'liked_post'
                        );
                        $add_activity  = Wo_RegisterActivity($activity_data);
                  }
                  $notification_data_array = array(
                        'recipient_id' => $user_id,
                        'post_id' => $post_id,
                        'type' => 'liked_post',
                        'text' => $text,
                        'type2' => $type2,
                        'url' => 'index.php?tab1=post&id=' . $post_id
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'liked';
            }
      }
}
function Wo_CountLikes($post_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT COUNT(`id`) AS `likes` FROM " . T_LIKES . " WHERE `post_id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['likes'];
      }
}
function Wo_IsLiked($post_id, $user_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `id` FROM " . T_LIKES . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_AddWonders($post_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($post_id) or empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id        = Wo_Secure($post_id);
      $user_id        = Wo_GetUserIdFromPostId($post_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $post           = Wo_PostData($post_id);
      if (empty($user_id)) {
            $user_id = Wo_GetUserIdFromPageId($post['page_id']);
            if (empty($user_id)) {
                  return false;
            }
      }
      $text  = '';
      $type2 = '';
      if (isset($post['postText']) && !empty($post['postText'])) {
            $text = substr($post['postText'], 0, 10) . '..';
      }
      if (isset($post['postYoutube']) && !empty($post['postYoutube'])) {
            $type2 = 'post_youtube';
      } elseif (isset($post['postSoundCloud']) && !empty($post['postSoundCloud'])) {
            $type2 = 'post_soundcloud';
      } elseif (isset($post['postVine']) && !empty($post['postVine'])) {
            $type2 = 'post_vine';
      } elseif (isset($post['postFile']) && !empty($post['postFile'])) {
            if (strpos($post['postFile'], '_image') !== false) {
                  $type2 = 'post_image';
            } else if (strpos($post['postFile'], '_video') !== false) {
                  $type2 = 'post_video';
            } else if (strpos($post['postFile'], '_avatar') !== false) {
                  $type2 = 'post_avatar';
            } else if (strpos($post['postFile'], '_sound') !== false) {
                  $type2 = 'post_soundFile';
            } else if (strpos($post['postFile'], '_cover') !== false) {
                  $type2 = 'post_cover';
            } else {
                  $type2 = 'post_file';
            }
      }
      if (Wo_IsWondered($post_id, $logged_user_id) === true) {
            $query_one        = "DELETE FROM " . T_WONDERS . " WHERE `post_id` = {$post_id} AND `user_id` = {$logged_user_id}";
            $query_delete_one = mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$user_id} AND `type` = 'wondered_post' ");
            $delete_activity  = Wo_DeleteActivity($post_id, $logged_user_id, 'wondered_post');
            $sql_query_one    = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unwonder';
            }
      } else {
            $query_two     = "INSERT INTO " . T_WONDERS . " (`user_id`, `post_id`) VALUES ({$logged_user_id}, {$post_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  if ($type2 != 'post_avatar') {
                        $activity_data = array(
                              'post_id' => $post_id,
                              'user_id' => $logged_user_id,
                              'post_user_id' => $user_id,
                              'activity_type' => 'wondered_post'
                        );
                        $add_activity  = Wo_RegisterActivity($activity_data);
                  }
                  $notification_data_array = array(
                        'recipient_id' => $user_id,
                        'post_id' => $post_id,
                        'type' => 'wondered_post',
                        'text' => $text,
                        'type2' => $type2,
                        'url' => 'index.php?tab1=post&id=' . $post_id
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'wonder';
            }
      }
}
function Wo_CountWonders($post_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT COUNT(`id`) AS `wonders` FROM " . T_WONDERS . " WHERE `post_id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['wonders'];
      }
}
function Wo_IsWondered($post_id, $user_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `id` FROM " . T_WONDERS . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_GetPostLikes($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $data          = array();
      $query_one     = "SELECT `user_id` FROM " . T_LIKES . " WHERE `post_id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetPostWonders($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $data          = array();
      $query_one     = "SELECT `user_id` FROM " . T_WONDERS . " WHERE `post_id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_AddShare($post_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() !== true) {
            return false;
      }
      if (!isset($post_id) or empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id        = Wo_Secure($post_id);
      $user_id        = Wo_GetUserIdFromPostId($post_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $post           = Wo_PostData($post_id);
      if (empty($user_id)) {
            $user_id = Wo_GetUserIdFromPageId($post['page_id']);
            if (empty($user_id)) {
                  return false;
            }
      }
      $text  = '';
      $type2 = '';
      if (isset($post['postText']) && !empty($post['postText'])) {
            $text = substr($post['postText'], 0, 10) . '..';
      }
      if (isset($post['postYoutube']) && !empty($post['postYoutube'])) {
            $type2 = 'post_youtube';
      } elseif (isset($post['postSoundCloud']) && !empty($post['postSoundCloud'])) {
            $type2 = 'post_soundcloud';
      } elseif (isset($post['postVine']) && !empty($post['postVine'])) {
            $type2 = 'post_vine';
      } elseif (isset($post['postFile']) && !empty($post['postFile'])) {
            if (strpos($post['postFile'], '_image') !== false) {
                  $type2 = 'post_image';
            } else if (strpos($post['postFile'], '_video') !== false) {
                  $type2 = 'post_video';
            } else if (strpos($post['postFile'], '_avatar') !== false) {
                  $type2 = 'post_avatar';
            } else if (strpos($post['postFile'], '_sound') !== false) {
                  $type2 = 'post_soundFile';
            } else if (strpos($post['postFile'], '_cover') !== false) {
                  $type2 = 'post_cover';
            } else {
                  $type2 = 'post_file';
            }
      }
      if (Wo_IsShared($post_id, $logged_user_id)) {
            $query_one        = "DELETE FROM " . T_POSTS . " WHERE `post_id` = {$post_id} AND `user_id` = {$logged_user_id} AND `postShare` = 1";
            $query_delete_one = mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$user_id} AND `type` = 'share_post'");
            $delete_activity  = Wo_DeleteActivity($post_id, $logged_user_id, 'shared_post');
            $sql_query_one    = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unshare';
            }
      } else {
            $query_two        = "INSERT INTO " . T_POSTS . " (`user_id`, `post_id`, `time`, `postShare`) VALUES ({$logged_user_id}, {$post_id}, " . time() . ", 1)";
            $sql_query_two    = mysqli_query($sqlConnect, $query_two);
            $inserted_post_id = mysqli_insert_id($sqlConnect);
            if ($sql_query_two) {
                  if ($type2 != 'post_avatar') {
                        $activity_data = array(
                              'post_id' => $post_id,
                              'user_id' => $logged_user_id,
                              'post_user_id' => $user_id,
                              'activity_type' => 'shared_post'
                        );
                        $add_activity  = Wo_RegisterActivity($activity_data);
                  }
                  $notification_data_array = array(
                        'recipient_id' => $user_id,
                        'post_id' => $post_id,
                        'type' => 'share_post',
                        'text' => $text,
                        'type2' => $type2,
                        'url' => 'index.php?tab1=post&id=' . $inserted_post_id
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'share';
            }
      }
}
function Wo_CountShares($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT COUNT(`id`) AS `shares` FROM " . T_POSTS . " WHERE `post_id` = {$post_id} AND `postShare` = 1";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['shares'];
      }
}
function Wo_IsShared($post_id, $user_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `id` FROM " . T_POSTS . " WHERE `post_id`= {$post_id} AND `postShare` = 1 AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_RegisterPostComment($data = array()) {
      global $sqlConnect, $wo;
      if (empty($data['post_id']) || !is_numeric($data['post_id']) || $data['post_id'] < 0) {
            return false;
      }
      if (empty($data['text'])) {
            return false;
      }
      if (empty($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] < 0) {
            return false;
      }
      if (!empty($data['page_id'])) {
            if (Wo_IsPageOnwer($data['page_id']) === false) {
                  $data['page_id'] = 0;
            }
      }
      if (!empty($data['text'])) {
            if ($wo['config']['maxCharacters'] > 0) {
                  if (strlen($data['text']) > $wo['config']['maxCharacters']) {
                        return false;
                  }
            }
            $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
            $i          = 0;
            preg_match_all($link_regex, $data['text'], $matches);
            foreach ($matches[0] as $match) {
                  $match_url    = strip_tags($match);
                  $syntax       = '[a]' . urlencode($match_url) . '[/a]';
                  $data['text'] = str_replace($match, $syntax, $data['text']);
            }
            $mention_regex = '/@([A-Za-z0-9_]+)/i';
            preg_match_all($mention_regex, $data['text'], $matches);
            foreach ($matches[1] as $match) {
                  $match         = Wo_Secure($match);
                  $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
                  $match_search  = '@' . $match;
                  $match_replace = '@[' . $match_user['user_id'] . ']';
                  if (isset($match_user['user_id'])) {
                        $data['text'] = str_replace($match_search, $match_replace, $data['text']);
                        $mentions[]   = $match_user['user_id'];
                  }
            }
      }
      $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
      preg_match_all($hashtag_regex, $data['text'], $matches);
      foreach ($matches[1] as $match) {
            if (!is_numeric($match)) {
                  $hashdata = Wo_GetHashtag($match);
                  if (is_array($hashdata)) {
                        $match_search      = '#' . $match;
                        $match_replace     = '#[' . $hashdata['id'] . ']';
                        $data['text']      = str_replace($match_search, $match_replace, $data['text']);
                        $hashtag_query     = "UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                        $hashtag_sql_query = mysqli_query($sqlConnect, $hashtag_query);
                  }
            }
      }
      $post    = Wo_PostData($data['post_id']);
      $text    = '';
      $type2   = '';
      $page_id = '';
      if (!empty($post['page_id']) && $post['page_id'] > 0) {
            $page_id = $post['page_id'];
      }
      if (isset($post['postText']) && !empty($post['postText'])) {
            $text = substr($post['postText'], 0, 10) . '..';
      }
      if (isset($post['postYoutube']) && !empty($post['postYoutube'])) {
            $type2 = 'post_youtube';
      } elseif (isset($post['postSoundCloud']) && !empty($post['postSoundCloud'])) {
            $type2 = 'post_soundcloud';
      } elseif (isset($post['postVine']) && !empty($post['postVine'])) {
            $type2 = 'post_vine';
      } elseif (isset($post['postFile']) && !empty($post['postFile'])) {
            if (strpos($post['postFile'], '_image') !== false) {
                  $type2 = 'post_image';
            } else if (strpos($post['postFile'], '_video') !== false) {
                  $type2 = 'post_video';
            } else if (strpos($post['postFile'], '_avatar') !== false) {
                  $type2 = 'post_avatar';
            } else if (strpos($post['postFile'], '_sound') !== false) {
                  $type2 = 'post_soundFile';
            } else if (strpos($post['postFile'], '_cover') !== false) {
                  $type2 = 'post_cover';
            } else {
                  $type2 = 'post_file';
            }
      }
      $user_id = Wo_GetUserIdFromPostId($data['post_id']);
      if (empty($user_id)) {
            $user_id = Wo_GetUserIdFromPageId($post['page_id']);
            if (empty($user_id)) {
                  return false;
            }
      }
      $fields       = '`' . implode('`, `', array_keys($data)) . '`';
      $comment_data = '\'' . implode('\', \'', $data) . '\'';
      $query        = mysqli_query($sqlConnect, "INSERT INTO  " . T_COMMENTS . " ({$fields}) VALUES ({$comment_data})");
      if ($query) {
            $inserted_comment_id     = mysqli_insert_id($sqlConnect);
            $activity_data           = array(
                  'post_id' => $data['post_id'],
                  'user_id' => $data['user_id'],
                  'post_user_id' => $user_id,
                  'activity_type' => 'commented_post'
            );
            $add_activity            = Wo_RegisterActivity($activity_data);
            $notification_data_array = array(
                  'recipient_id' => $user_id,
                  'post_id' => $data['post_id'],
                  'type' => 'comment',
                  'text' => $text,
                  'type2' => $type2,
                  'url' => 'index.php?tab1=post&id=' . $data['post_id'] . '&ref=' . $inserted_comment_id
            );
            Wo_RegisterNotification($notification_data_array);
            if (isset($mentions) && is_array($mentions)) {
                  foreach ($mentions as $mention) {
                        $notification_data_array = array(
                              'recipient_id' => $mention,
                              'type' => 'comment_mention',
                              'page_id' => $page_id,
                              'url' => 'index.php?tab1=post&id=' . $data['post_id']
                        );
                        Wo_RegisterNotification($notification_data_array);
                  }
            }
            return $inserted_comment_id;
      }
}
function Wo_GetPostComments($post_id = 0, $limit = 5) {
      global $sqlConnect;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      $post_id = Wo_Secure($post_id);
      $data    = array();
      $query   = "SELECT `id` FROM " . T_COMMENTS . " WHERE `post_id` = {$post_id} ORDER BY `id` ASC";
      if (($comments_num = Wo_CountPostComment($post_id)) > $limit) {
            $query .= " LIMIT " . ($comments_num - $limit) . ", {$limit} ";
      }
      $query_one = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $data[] = Wo_GetPostComment($fetched_data['id']);
      }
      return $data;
}
function Wo_GetPostComment($comment_id = 0) {
      global $wo, $sqlConnect;
      if (empty($comment_id) || !is_numeric($comment_id) || $comment_id < 0) {
            return false;
      }
      $query_one    = mysqli_query($sqlConnect, "SELECT * FROM " . T_COMMENTS . " WHERE `id` = {$comment_id} ");
      $fetched_data = mysqli_fetch_assoc($query_one);
      if (!empty($fetched_data['page_id'])) {
            $fetched_data['publisher'] = Wo_PageData($fetched_data['page_id']);
            $fetched_data['url']       = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['publisher']['page_name']);
      } else {
            $fetched_data['publisher'] = Wo_UserData($fetched_data['user_id']);
            $fetched_data['url']       = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['publisher']['username']);
      }
      $fetched_data['Orginaltext']         = Wo_EditMarkup($fetched_data['text']);
      $fetched_data['Orginaltext']         = str_replace('<br>', "\n", $fetched_data['Orginaltext']);
      $fetched_data['text']                = Wo_Markup($fetched_data['text']);
      $fetched_data['text']                = Wo_Emo($fetched_data['text']);
      $fetched_data['onwer']               = false;
      $fetched_data['post_onwer']          = false;
      $fetched_data['comment_likes']       = Wo_CountCommentLikes($fetched_data['id']);
      $fetched_data['comment_wonders']     = Wo_CountCommentWonders($fetched_data['id']);
      $fetched_data['is_comment_wondered'] = false;
      $fetched_data['is_comment_liked']    = false;
      if (Wo_IsLogged() === true) {
            $fetched_data['onwer']               = ($fetched_data['publisher']['user_id'] == $wo['user']['user_id']) ? true : false;
            $fetched_data['post_onwer']          = (Wo_IsPostOnwer($fetched_data['post_id'], $wo['user']['user_id'])) ? true : false;
            $fetched_data['is_comment_wondered'] = (Wo_IsCommentWondered($fetched_data['id'], $wo['user']['user_id'])) ? true : false;
            $fetched_data['is_comment_liked']    = (Wo_IsCommentLiked($fetched_data['id'], $wo['user']['user_id'])) ? true : false;
      }
      return $fetched_data;
}
function Wo_CountPostComment($post_id = '') {
      global $sqlConnect;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS `comments` FROM " . T_COMMENTS . " WHERE `post_id` = {$post_id} ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['comments'];
}
function Wo_DeletePostComment($comment_id = '') {
      global $wo, $sqlConnect;
      if ($comment_id < 0 || empty($comment_id) || !is_numeric($comment_id)) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $post_id        = Wo_GetPostIdFromCommentId($comment_id);
      $query_one      = mysqli_query($sqlConnect, "SELECT `id`, `user_id` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id} AND `user_id` = {$logged_user_id}");
      if (mysqli_num_rows($query_one) > 0 || Wo_IsPostOnwer($post_id, $logged_user_id) === true) {
            $query_delete = mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENTS . " WHERE `id` = {$comment_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENT_LIKES . " WHERE `comment_id` = {$comment_id}");
            $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENT_WONDERS . " WHERE `comment_id` = {$comment_id}");
            if ($query_delete) {
                  $query_two = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_COMMENTS_REPLIES . " WHERE `comment_id` = {$comment_id}");
                  while ($fetched_data = mysqli_fetch_assoc($query_two)) {
                        Wo_DeleteCommentReply($fetched_data['id']);
                  }
                  $delete_activity = Wo_DeleteActivity($post_id, $logged_user_id, 'commented_post');
                  return true;
            }
      } else {
            return false;
      }
}
function Wo_DeletePostReplyComment($comment_id = '') {
      global $wo, $sqlConnect;
      if ($comment_id < 0 || empty($comment_id) || !is_numeric($comment_id)) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $query_one      = mysqli_query($sqlConnect, "SELECT `id`, `user_id` FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$comment_id} AND `user_id` = {$logged_user_id}");
      if (mysqli_num_rows($query_one) > 0) {
            $query_delete = mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$comment_id}");
            return true;
      } else {
            return false;
      }
}
function Wo_UpdateComment($data = array()) {
      global $wo, $sqlConnect;
      if ($data['comment_id'] < 0 || empty($data['comment_id']) || !is_numeric($data['comment_id'])) {
            return false;
      }
      if (empty($data['text'])) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }
      $page_id = 0;
      if (!empty($data['page_id'])) {
            $page_id = Wo_Secure($data['page_id']);
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $comment_id   = Wo_Secure($data['comment_id']);
      $comment_text = Wo_Secure($data['text']);
      $query        = mysqli_query($sqlConnect, "SELECT `id`, `user_id` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id} AND `user_id` = {$user_id}");
      if (mysqli_num_rows($query) > 0) {
            if (!empty($comment_text)) {
                  if ($wo['config']['maxCharacters'] > 0) {
                        if (strlen($data['text']) > $wo['config']['maxCharacters']) {
                              return false;
                        }
                  }
                  $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
                  $i          = 0;
                  preg_match_all($link_regex, $comment_text, $matches);
                  foreach ($matches[0] as $match) {
                        $match_url    = strip_tags($match);
                        $syntax       = '[a]' . urlencode($match_url) . '[/a]';
                        $comment_text = str_replace($match, $syntax, $comment_text);
                  }
                  $mention_regex = '/@([A-Za-z0-9_]+)/i';
                  preg_match_all($mention_regex, $comment_text, $matches);
                  foreach ($matches[1] as $match) {
                        $match         = Wo_Secure($match);
                        $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
                        $match_search  = '@' . $match;
                        $match_replace = '@[' . $match_user['user_id'] . ']';
                        if (isset($match_user['user_id'])) {
                              $comment_text = str_replace($match_search, $match_replace, $comment_text);
                              $mentions[]   = $match_user['user_id'];
                        }
                  }
            }
            $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
            preg_match_all($hashtag_regex, $comment_text, $matches);
            foreach ($matches[1] as $match) {
                  if (!is_numeric($match)) {
                        $hashdata = Wo_GetHashtag($match);
                        if (is_array($hashdata)) {
                              $match_search      = '#' . $match;
                              $match_replace     = '#[' . $hashdata['id'] . ']';
                              $comment_text      = str_replace($match_search, $match_replace, $comment_text);
                              $hashtag_query     = "UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                              $hashtag_sql_query = mysqli_query($sqlConnect, $hashtag_query);
                        }
                  }
            }
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_COMMENTS . " SET `text` = '{$comment_text}' WHERE `id` = {$comment_id}");
            if ($query_one) {
                  if (isset($mentions) && is_array($mentions)) {
                        foreach ($mentions as $mention) {
                              $notification_data_array = array(
                                    'recipient_id' => $mention,
                                    'type' => 'comment_mention',
                                    'page_id' => $page_id,
                                    'url' => 'index.php?tab1=post&id=' . Wo_GetPostIdFromCommentId($data['comment_id'])
                              );
                              Wo_RegisterNotification($notification_data_array);
                        }
                  }
                  $query                = mysqli_query($sqlConnect, "SELECT `text` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id}");
                  $fetched_data         = mysqli_fetch_assoc($query);
                  $fetched_data['text'] = Wo_Markup($fetched_data['text']);
                  $fetched_data['text'] = Wo_Emo($fetched_data['text']);
                  return $fetched_data['text'];
            }
      } else {
            return false;
      }
}
function Wo_UpdatePostPrivacy($data = array()) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if ($data['post_id'] < 0 || empty($data['post_id']) || !is_numeric($data['post_id'])) {
            return false;
      }
      if (!is_numeric($data['privacy_type'])) {
            return false;
      }
      $privacy_type = Wo_Secure($data['privacy_type']);
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $post_id      = Wo_Secure($data['post_id']);
      $query        = mysqli_query($sqlConnect, "SELECT `id`, `user_id` FROM " . T_POSTS . " WHERE `id` = {$post_id} OR `post_id` = {$post_id}  AND `user_id` = {$user_id}");
      if (mysqli_num_rows($query) == 1) {
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `postPrivacy` = '{$privacy_type}' WHERE `id` = {$post_id}");
            if ($query_one) {
                  if ($wo['config']['cacheSystem'] == 1) {
                        $cache->delete(md5($data['post_id']) . '_P_Data.tmp');
                  }
                  return $privacy_type;
            }
      } else {
            return false;
      }
}
function Wo_UpdatePost($data = array()) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if ($data['post_id'] < 0 || empty($data['post_id']) || !is_numeric($data['post_id'])) {
            return false;
      }
      if (empty($data['text'])) {
            return false;
      }
      $page_id = 0;
      if (!empty($data['page_id'])) {
            $page_id = Wo_Secure($data['page_id']);
      }
      $post_text = Wo_Secure($data['text']);
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $post_id   = Wo_Secure($data['post_id']);
      $query     = mysqli_query($sqlConnect, "SELECT `id`, `user_id` FROM " . T_POSTS . " WHERE `id` = {$post_id} OR `post_id` = {$post_id} AND `user_id` = {$user_id} OR `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id})");
      if (mysqli_num_rows($query) > 0) {
            if (!empty($post_text)) {
                  if ($wo['config']['maxCharacters'] > 0) {
                        if (strlen($post_text) > $wo['config']['maxCharacters']) {
                        }
                  }
                  $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
                  $i          = 0;
                  preg_match_all($link_regex, $post_text, $matches);
                  foreach ($matches[0] as $match) {
                        $match_url = strip_tags($match);
                        $syntax    = '[a]' . urlencode($match_url) . '[/a]';
                        $post_text = str_replace($match, $syntax, $post_text);
                  }
                  $mention_regex = '/@([A-Za-z0-9_]+)/i';
                  preg_match_all($mention_regex, $post_text, $matches);
                  foreach ($matches[1] as $match) {
                        $match         = Wo_Secure($match);
                        $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
                        $match_search  = '@' . $match;
                        $match_replace = '@[' . $match_user['user_id'] . ']';
                        if (isset($match_user['user_id'])) {
                              $post_text  = str_replace($match_search, $match_replace, $post_text);
                              $mentions[] = $match_user['user_id'];
                        }
                  }
            }
            $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
            preg_match_all($hashtag_regex, $post_text, $matches);
            foreach ($matches[1] as $match) {
                  if (!is_numeric($match)) {
                        $hashdata = Wo_GetHashtag($match);
                        if (is_array($hashdata)) {
                              $match_search      = '#' . $match;
                              $match_replace     = '#[' . $hashdata['id'] . ']';
                              $post_text         = str_replace($match_search, $match_replace, $post_text);
                              $hashtag_query     = "UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                              $hashtag_sql_query = mysqli_query($sqlConnect, $hashtag_query);
                        }
                  }
            }
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `postText` = '{$post_text}' WHERE `id` = {$post_id}");
            if ($query_one) {
                  if ($wo['config']['cacheSystem'] == 1) {
                        $cache->delete(md5($data['post_id']) . '_P_Data.tmp');
                  }
                  if (isset($mentions) && is_array($mentions)) {
                        foreach ($mentions as $mention) {
                              $notification_data_array = array(
                                    'recipient_id' => $mention,
                                    'type' => 'post_mention',
                                    'page_id' => $page_id,
                                    'url' => 'index.php?tab1=post&id=' . $post_id
                              );
                              Wo_RegisterNotification($notification_data_array);
                        }
                  }
                  $query                    = mysqli_query($sqlConnect, "SELECT `postText` FROM " . T_POSTS . " WHERE `id` = {$post_id}");
                  $fetched_data             = mysqli_fetch_assoc($query);
                  $fetched_data['postText'] = Wo_Markup($fetched_data['postText']);
                  $fetched_data['postText'] = Wo_Emo($fetched_data['postText']);
                  return $fetched_data['postText'];
            }
      } else {
            return false;
      }
}
function Wo_SavePosts($post_data = array()) {
      global $wo, $sqlConnect;
      if (empty($post_data)) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $post_id = Wo_Secure($post_data['post_id']);
      if (Wo_IsPostSaved($post_id, $user_id)) {
            $query_one     = "DELETE FROM " . T_SAVED_POSTS . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unsaved';
            }
      } else {
            $query_two     = "INSERT INTO " . T_SAVED_POSTS . " (`user_id`, `post_id`) VALUES ({$user_id}, {$post_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  return 'saved';
            }
      }
}
function Wo_ReportPost($post_data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_data)) {
            return false;
      }
      if (Wo_PostExists($post_data['post_id']) === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $post_id = Wo_Secure($post_data['post_id']);
      if (Wo_IsPostRepotred($post_id, $user_id)) {
            $query_one     = "DELETE FROM " . T_REPORTS . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unreport';
            }
      } else {
            $query_two     = "INSERT INTO " . T_REPORTS . " (`user_id`, `post_id`, `time`) VALUES ({$user_id}, {$post_id}, " . time() . ")";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  return 'report';
            }
      }
}
function Wo_CountUnseenReports() {
      global $wo, $sqlConnect;
      $query_one    = "SELECT COUNT(`id`) AS `reports` FROM " . T_REPORTS . " WHERE `seen` = 0 ";
      $sql          = mysqli_query($sqlConnect, $query_one);
      $fetched_data = mysqli_fetch_assoc($sql);
      return $fetched_data['reports'];
}
function Wo_UpdateSeenReports() {
      global $wo, $sqlConnect;
      $query_one = " UPDATE " . T_REPORTS . " SET `seen` = 1 WHERE `seen` = 0";
      $sql       = mysqli_query($sqlConnect, $query_one);
      if ($sql) {
            return true;
      }
}
function Wo_DeleteReport($report_id = '') {
      global $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $report_id = Wo_Secure($report_id);
      $query     = mysqli_query($sqlConnect, "DELETE FROM " . T_REPORTS . " WHERE `id` = {$report_id}");
      if ($query) {
            return true;
      }
}
function Wo_GetReports() {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT * FROM " . T_REPORTS . " ORDER BY `id` DESC";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $fetched_data['reporter'] = Wo_UserData($fetched_data['user_id']);
            $fetched_data['story']    = Wo_PostData($fetched_data['post_id']);
            $data[]                   = $fetched_data;
      }
      return $data;
}
function Wo_IsPostRepotred($post_id = '', $user_id = '') {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_REPORTS . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_CountUnseenVerifications() {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      $query_one    = "SELECT COUNT(`id`) AS `verifications` FROM " . T_VERIFICATION_REQUESTS . " WHERE `seen` = 0 ";
      $sql          = mysqli_query($sqlConnect, $query_one);
      $fetched_data = mysqli_fetch_assoc($sql);
      return $fetched_data['verifications'];
}
function Wo_UpdateSeenVerifications() {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      $query_one = " UPDATE " . T_VERIFICATION_REQUESTS . " SET `seen` = 1 WHERE `seen` = 0";
      $sql       = mysqli_query($sqlConnect, $query_one);
      if ($sql) {
            return true;
      }
}
function Wo_DeleteVerificationRequest($id = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id)) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "DELETE FROM " . T_VERIFICATION_REQUESTS . " WHERE `id` = {$id}");
      if ($query) {
            return true;
      }
}
function Wo_DeleteVerification($id = 0, $type = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) || empty($type)) {
            return false;
      }
      if (!in_array($type, array(
            'User',
            'Page'
      ))) {
            return false;
      }
      $id          = Wo_Secure($id);
      $update_data = array(
            'verified' => 0
      );
      $update      = false;
      if ($type == 'Page') {
            $update = mysqli_query($sqlConnect, "UPDATE " . T_PAGES . " SET `verified` = '0' WHERE `page_id` = {$id}");
      } else if ($type == 'User') {
            $update = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `verified` = '0' WHERE `user_id` = {$id}");
      }
      if ($update) {
            return true;
      }
}
function Wo_RemoveVerificationRequest($id = 0, $type = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) || empty($type)) {
            return false;
      }
      if (!in_array($type, array(
            'User',
            'Page'
      ))) {
            return false;
      }
      $id = Wo_Secure($id);
      if ($type == 'Page') {
            if (Wo_IsPageOnwer($id) === false) {
                  return false;
            }
            $type_id = '`page_id`';
            $type_2  = 'page';
      } else if ($type == 'User') {
            if (Wo_IsOnwer($id) === false) {
                  return false;
            }
            $type_id = '`user_id`';
            $type_2  = 'user';
      }
      $delete_query = mysqli_query($sqlConnect, "DELETE FROM " . T_VERIFICATION_REQUESTS . " WHERE {$type_id} = {$id} AND `type` = '{$type_2}'");
      if ($delete_query) {
            return true;
      }
}
function Wo_VerifyUser($id = 0, $verification_id = 0, $type = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) || empty($type) || empty($verification_id)) {
            return false;
      }
      if (!in_array($type, array(
            'User',
            'Page'
      ))) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      $id          = Wo_Secure($id);
      $update_data = array(
            'verified' => 1
      );
      $update      = false;
      if ($type == 'Page') {
            $update = Wo_UpdatePageData($id, $update_data);
      } else if ($type == 'User') {
            $update = Wo_UpdateUserData($id, $update_data);
      }
      if ($update) {
            if (Wo_DeleteVerificationRequest($verification_id) === true) {
                  return true;
            }
      }
}
function Wo_RequestVerification($id = 0, $type = '') {
      global $sqlConnect;
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      if (empty($type)) {
            return false;
      }
      if (!in_array($type, array(
            'User',
            'Page'
      ))) {
            return false;
      }
      if (Wo_IsVerificationRequests($id, $type) === true) {
            return false;
      }
      $values = '';
      if ($type == 'Page') {
            if (Wo_IsPageOnwer($id) === false) {
                  return false;
            }
            $values = "`page_id`,`type`";
      } else if ($type == 'User') {
            if (Wo_IsOnwer($id) === false) {
                  return false;
            }
            $values = "`user_id`,`type`";
      }
      $query_one = mysqli_query($sqlConnect, "INSERT INTO " . T_VERIFICATION_REQUESTS . " ($values) VALUES({$id},'{$type}') ");
      if ($query_one) {
            return true;
      }
}
function Wo_IsVerificationRequests($id = '', $type = '') {
      global $sqlConnect;
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      if (empty($type)) {
            return false;
      }
      if (!in_array($type, array(
            'User',
            'Page'
      ))) {
            return false;
      }
      $id    = Wo_Secure($id);
      $type  = Wo_Secure($type);
      $where = '';
      if ($type == 'Page') {
            $where = " `page_id` = {$id} AND `type` = 'page'";
      } else if ($type == 'User') {
            $where = " `user_id` = {$id} AND `type` = 'user'";
      }
      if (empty($where)) {
            return false;
      }
      $query_one     = "SELECT `id` as count FROM " . T_VERIFICATION_REQUESTS . " WHERE{$where}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) > 0) {
            return true;
      }
}
function Wo_GetVerificationButton($id, $type) {
      global $sqlConnect, $wo;
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      if (empty($type)) {
            return false;
      }
      $verified_type = 0;
      if ($type == 'Page') {
            $wo['verification']       = Wo_PageData($id);
            $wo['verification']['id'] = $wo['verification']['page_id'];
      } else if ($type == 'User') {
            $wo['verification']       = Wo_UserData($id);
            $wo['verification']['id'] = $wo['verification']['user_id'];
      }
      $wo['verification']['type'] = $type;
      $pending                    = 'buttons/pending-verification';
      $remove                     = 'buttons/remove-verification';
      $request                    = 'buttons/request-verification';
      $verified                   = $wo['verification']['verified'];
      if (Wo_IsVerificationRequests($id, $type)) {
            return Wo_LoadPage($pending);
      } else if ($verified == 1) {
            return Wo_LoadPage($remove);
      } else {
            return Wo_LoadPage($request);
      }
}
function Wo_GetVerifications() {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT * FROM " . T_VERIFICATION_REQUESTS . " ORDER BY `id` DESC";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            if (!empty($fetched_data['user_id'])) {
                  $fetched_data['request_from']       = Wo_UserData($fetched_data['user_id']);
                  $fetched_data['request_from']['id'] = $fetched_data['user_id'];
            } else if (!empty($fetched_data['page_id'])) {
                  $fetched_data['request_from']       = Wo_PageData($fetched_data['page_id']);
                  $fetched_data['request_from']['id'] = $fetched_data['page_id'];
            } else {
                  return false;
            }
            $fetched_data['type'] = ($fetched_data['type'] == 'User') ? $wo['lang']['user'] : $wo['lang']['page'];
            $data[]               = $fetched_data;
      }
      return $data;
}
function Wo_GetAllPosts($posts = array('limit' => 10, 'after_user_id' => 0)) {
      global $wo, $sqlConnect;
      $data     = array();
      $subquery = '';
      $limit    = Wo_Secure($posts['limit']);
      if (isset($posts['after_post_id']) && !empty($posts['after_post_id']) && $posts['after_post_id'] > 0) {
            $after_post_id = Wo_Secure($posts['after_post_id']);
            $subquery      = " WHERE `id` < {$after_post_id}";
      }
      $query_one = " SELECT `id` FROM " . T_POSTS . " {$subquery} ORDER BY `id` DESC LIMIT {$limit}";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = Wo_PostData($fetched_data['id'], 'admin');
      }
      return $data;
}
function Wo_IsPostSaved($post_id, $user_id) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_SAVED_POSTS . " WHERE `post_id` = {$post_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_GetSavedPosts($user_id) {
      global $wo, $sqlConnect;
      $data  = array();
      $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_SAVED_POSTS . " WHERE `user_id` = {$user_id} ORDER BY id DESC");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_PostData($fetched_data['post_id']);
      }
      return $data;
}
function Wo_GetPostIdFromCommentId($comment_id = 0) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $query_one     = "SELECT `post_id` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['post_id'];
      }
}
function Wo_GetUserIdFromCommentId($comment_id = 0) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $query_one     = "SELECT `user_id` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['user_id'];
      }
}
function Wo_AddCommentLikes($comment_id, $text = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($comment_id) or empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id          = Wo_Secure($comment_id);
      $user_id             = Wo_Secure($wo['user']['user_id']);
      $comment_timeline_id = Wo_GetUserIdFromCommentId($comment_id);
      $post_id             = Wo_GetPostIdFromCommentId($comment_id);
      $page_id             = '';
      $post_data           = Wo_PostData($post_id);
      if (!empty($post_data['page_id'])) {
            $page_id = $post_data['page_id'];
      }
      if (Wo_IsPageOnwer($post_data['page_id']) === false) {
            $page_id = 0;
      }
      if (empty($comment_timeline_id)) {
            return false;
      }
      if (isset($text) && !empty($text)) {
            $text = substr($text, 0, 10) . '..';
      }
      $text = Wo_Secure($text);
      if (Wo_IsCommentLiked($comment_id, $user_id) === true) {
            $query_one = "DELETE FROM " . T_COMMENT_LIKES . " WHERE `comment_id` = {$comment_id} AND `user_id` = {$user_id}";
            mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$comment_timeline_id} AND `type` = 'liked_comment'");
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unliked';
            }
      } else {
            $query_two     = "INSERT INTO " . T_COMMENT_LIKES . " (`user_id`, `post_id`, `comment_id`) VALUES ({$user_id},{$post_id},{$comment_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  $notification_data_array = array(
                        'recipient_id' => $comment_timeline_id,
                        'post_id' => $post_id,
                        'type' => 'liked_comment',
                        'text' => $text,
                        'page_id' => $page_id,
                        'url' => 'index.php?tab1=post&id=' . $post_id . '&ref=' . $comment_id
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'liked';
            }
      }
}
function Wo_CountCommentLikes($comment_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $query_one     = "SELECT COUNT(`id`) AS `likes` FROM " . T_COMMENT_LIKES . " WHERE `comment_id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['likes'];
      }
}
function Wo_IsCommentLiked($comment_id, $user_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_COMMENT_LIKES . " WHERE `comment_id` = {$comment_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_AddCommentWonders($comment_id, $text = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($comment_id) or empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id      = Wo_Secure($comment_id);
      $user_id         = Wo_Secure($wo['user']['user_id']);
      $comment_user_id = Wo_GetUserIdFromCommentId($comment_id);
      $post_id         = Wo_GetPostIdFromCommentId($comment_id);
      $page_id         = '';
      $post_data       = Wo_PostData($post_id);
      if (!empty($post_data['page_id'])) {
            $page_id = $post_data['page_id'];
      }
      if (Wo_IsPageOnwer($post_data['page_id']) === false) {
            $page_id = 0;
      }
      if (empty($comment_user_id)) {
            return false;
      }
      if (isset($text) && !empty($text)) {
            $text = substr($text, 0, 10) . '..';
      }
      $text = Wo_Secure($text);
      if (Wo_IsCommentWondered($comment_id, $wo['user']['user_id']) === true) {
            $query_one = "DELETE FROM " . T_COMMENT_WONDERS . " WHERE `comment_id` = {$comment_id} AND `user_id` = {$user_id}";
            mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$comment_user_id} AND `type` = 'wondered_comment'");
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unwonder';
            }
      } else {
            $query_two     = "INSERT INTO " . T_COMMENT_WONDERS . " (`user_id`, `post_id`, `comment_id`) VALUES ({$user_id}, {$post_id}, {$comment_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  $notification_data_array = array(
                        'recipient_id' => $comment_user_id,
                        'post_id' => $post_id,
                        'type' => 'wondered_comment',
                        'text' => $text,
                        'page_id' => $page_id,
                        'url' => 'index.php?tab1=post&id=' . $post_id . '&ref=' . $comment_id
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'wonder';
            }
      }
}
function Wo_CountCommentWonders($comment_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $query_one     = "SELECT COUNT(`id`) AS `likes` FROM " . T_COMMENT_WONDERS . " WHERE `comment_id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['likes'];
      }
}
function Wo_IsCommentWondered($comment_id, $user_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_COMMENT_WONDERS . " WHERE `comment_id` = {$comment_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_GetCommentLikes($comment_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $data          = array();
      $query_one     = "SELECT `user_id` FROM " . T_COMMENT_LIKES . " WHERE `comment_id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetCommentWonders($comment_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $data          = array();
      $query_one     = "SELECT `user_id` FROM " . T_COMMENT_WONDERS . " WHERE `comment_id` = {$comment_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wa_GetTrendingHashs($type = 'latest', $limit = 5) {
      global $sqlConnect;
      $data = array();
      if (empty($type)) {
            return false;
      }
      if (empty($limit) or !is_numeric($limit) or $limit < 1) {
            $limit = 5;
      }
      if ($type == "latest") {
            $query = "SELECT * FROM " . T_HASHTAGS . " ORDER BY `last_trend_time` DESC LIMIT {$limit}";
      } elseif ($type == "popular") {
            $query = "SELECT * FROM " . T_HASHTAGS . " ORDER BY `trend_use_num` DESC LIMIT {$limit}";
      }
      $sql_query   = mysqli_query($sqlConnect, $query);
      $sql_numrows = mysqli_num_rows($sql_query);
      if ($sql_numrows > 0) {
            while ($sql_fetch = mysqli_fetch_assoc($sql_query)) {
                  $sql_fetch['url'] = Wo_SeoLink('index.php?tab1=hashtag&hash=' . $sql_fetch['tag']);
                  $data[]           = $sql_fetch;
            }
            return $data;
      }
}
function Wo_GetHashtagPosts($s_query, $after_post_id = 0, $limit = 5) {
      global $sqlConnect;
      $data         = array();
      $search_query = str_replace('#', '', Wo_Secure($s_query));
      $hashdata     = Wo_GetHashtag($search_query);
      if (is_array($hashdata) && count($hashdata) > 0) {
            $search_string = "#[" . $hashdata['id'] . "]";
            $query_one     = "SELECT id FROM " . T_POSTS . " WHERE `postText` LIKE '%{$search_string}%'";
            if (isset($after_post_id) && !empty($after_post_id) && is_numeric($after_post_id)) {
                  $after_post_id = Wo_Secure($after_post_id);
                  $query_one .= " AND id < {$after_post_id}";
            }
            $query_one .= " ORDER BY `id` DESC LIMIT {$limit}";
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
                  $posts = Wo_PostData($sql_fetch_one['id']);
                  if (is_array($posts)) {
                        $data[] = $posts;
                  }
            }
      }
      return $data;
}
function Wo_SearchForPosts($id = 0, $s_query = '', $limit = 5, $type = '') {
      global $sqlConnect;
      $data = array();
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) || !is_numeric($id) || $id < 1) {
            return false;
      }
      if ($type == 'page') {
            $query_type = "AND `page_id` = {$id}";
      } else if ($type == 'user') {
            $query_type = "AND `user_id` = {$id}";
      } else if ($type == 'group') {
            $query_type = "AND `group_id` = {$id}";
      } else {
            return false;
      }
      $search_query = Wo_Secure($s_query);
      $query_one    = "SELECT id FROM " . T_POSTS . " WHERE `postText` LIKE '%{$search_query}%' {$query_type}";
      $query_one .= " ORDER BY `id` DESC LIMIT {$limit}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
            $posts = Wo_PostData($sql_fetch_one['id']);
            if (is_array($posts)) {
                  $data[] = $posts;
            }
      }
      return $data;
}
function Wo_GetSerachHash($s_query) {
      global $sqlConnect;
      $search_query = str_replace('#', '', Wo_Secure($s_query));
      $data         = array();
      $query        = mysqli_query($sqlConnect, "SELECT * FROM " . T_HASHTAGS . " WHERE `tag` LIKE '%{$search_query}%' ORDER BY `trend_use_num` DESC LIMIT 10");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $fetched_data['url'] = Wo_SeoLink('index.php?tab1=hashtag&hash=' . $fetched_data['tag']);
            $data[]              = $fetched_data;
      }
      return $data;
}
function Wo_CountOnlineUsers() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $time         = time() - 60;
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) AS `online` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id`= {$user_id} AND `following_id` <> {$user_id} AND `active` = '1') AND `lastseen` > {$time} AND `active` = '1' ORDER BY `lastseen` DESC");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['online'];
}
function Wo_GetChatUsers($type) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data       = array();
      $time       = time() - 60;
      $user_id    = Wo_Secure($wo['user']['user_id']);
      $query_text = "SELECT `user_id` FROM " . T_USERS . " WHERE `user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `following_id` <> {$user_id} AND `active` = '1')";
      if ($type == 'online') {
            $query_text .= " AND `lastseen` > {$time}";
      } else if ($type == 'offline') {
            $query_text .= " AND `lastseen` < {$time}";
      }
      $query_text .= " AND `active` = '1' ORDER BY `lastseen` DESC LIMIT 5";
      $query = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_ChatSearchUsers($search_query = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data         = array();
      $time         = time() - 60;
      $search_query = Wo_Secure($search_query);
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query_one    = "SELECT `user_id` FROM " . T_USERS . " WHERE (`user_id` IN (SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `following_id` <> {$user_id} AND `active` = '1') AND `active` = '1'";
      if (isset($search_query) && !empty($search_query)) {
            $query_one .= " AND ((`username` LIKE '%$search_query%') OR CONCAT(`first_name`,  ' ', `last_name`) LIKE  '%{$search_query}%'))";
      }
      $query_one .= " ORDER BY `first_name` LIMIT 10";
      $query = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_UpdateStatus($status = 'online') {
      global $sqlConnect, $wo, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($status)) {
            return false;
      }
      $finel_status = '';
      $user_id      = Wo_Secure($wo['user']['user_id']);
      if ($status == 'online') {
            $finel_status = 0;
      } else if ($status == 'offline') {
            $finel_status = 1;
      }
      if (!is_numeric($finel_status)) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `status` = {$finel_status} WHERE `user_id` = {$user_id}");
      if ($query) {
            if ($wo['config']['cacheSystem'] == 1) {
                  $cache->delete(md5($wo['user']['user_id']) . '_U_Data.tmp');
            }
            return $finel_status;
      }
}
function Wo_IsOnline($user_id) {
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      $user_id  = Wo_Secure($user_id);
      $lastseen = Wo_UserData($user_id);
      $time     = time() - 60;
      if ($lastseen['lastseen'] < $time) {
            return false;
      } else {
            return true;
      }
}
function Wo_RightToLeft($type = '') {
      global $wo;
      $type = Wo_Secure($type);
      if ($wo['language_type'] == 'rtl') {
            if ($type == 'pull-right') {
                  return 'pull-left';
            }
            if ($type == 'pull-left') {
                  return 'pull-right';
            }
            if ($type == 'left-addon') {
                  return 'right-addon';
            }
            if ($type == 'text-right') {
                  return 'text-left';
            }
            if ($type == 'text-left') {
                  return 'text-right';
            }
            if ($type == 'right') {
                  return 'left';
            }
      } else {
            return $type;
      }
}
function Wo_IsTyping($recipient_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($recipient_id) || !is_numeric($recipient_id) || $recipient_id < 0) {
            return false;
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $recipient_id = Wo_Secure($recipient_id);
      $query        = "SELECT `is_typing` FROM " . T_FOLLOWERS . " WHERE follower_id = {$user_id} AND following_id = {$recipient_id} AND `is_typing` = 1";
      $query_one    = mysqli_query($sqlConnect, $query);
      return (Wo_Sql_Result($query_one, 0) == 1) ? true : false;
}
function Wo_RegisterTyping($recipient_id, $isTyping = 1) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($recipient_id) || !is_numeric($recipient_id) || $recipient_id < 0) {
            return false;
      }
      $user_id      = $wo['user']['user_id'];
      $recipient_id = Wo_Secure($recipient_id);
      $typing       = 1;
      if ($isTyping == 0) {
            $typing = 0;
      }
      if (Wo_IsFollowing($user_id, $recipient_id) === false) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "UPDATE " . T_FOLLOWERS . " SET `is_typing`  = " . $typing . " WHERE following_id = " . $wo['user']['user_id'] . "  AND follower_id = {$recipient_id}");
      if ($query) {
            return true;
      }
}
function Wo_DeleteAllTyping($user_id) {
      global $sqlConnect;
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      $query   = mysqli_query($sqlConnect, "UPDATE " . T_FOLLOWERS . " SET `is_typing` = 0 WHERE `following_id` = {$user_id}");
      if ($query) {
            return true;
      }
}
function Wo_UpdateAdsCode($update_data = array()) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      if (empty($update_data['type'])) {
            return false;
      }
      $type   = Wo_Secure($update_data['type']);
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . mysqli_real_escape_string($sqlConnect, $data) . '\'';
      }
      $query_text    = implode(', ', $update);
      $query_one     = " UPDATE " . T_ADS . " SET {$query_text} WHERE `type` = '{$type}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if ($sql_query_one) {
            return true;
      }
}
function Wo_GetAd($type, $admin = true) {
      global $sqlConnect;
      $type      = Wo_Secure($type);
      $query_one = "SELECT `code` FROM " . T_ADS . " WHERE `type` = '{$type}'";
      if ($admin === false) {
            $query_one .= " AND `active` = '1'";
      }
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      $fetched_data  = mysqli_fetch_assoc($sql_query_one);
      return $fetched_data['code'];
}
function Wo_IsAdActive($type) {
      global $sqlConnect;
      $query_one     = "SELECT COUNT(`id`) AS `count` FROM " . T_ADS . " WHERE `type` = '{$type}' AND `active` = '1' ";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      $fetched_data  = mysqli_fetch_assoc($sql_query_one);
      return $fetched_data['count'];
}
function Wo_UpdateAdActivation($type) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            return false;
      }
      if (Wo_IsAdActive($type)) {
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ADS . " SET `active` = '0' WHERE `type` = '{$type}'");
            return 'inactive';
      } else {
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ADS . " SET `active` = '1' WHERE `type` = '{$type}'");
            return 'active';
      }
}
function Wo_AddNewAnnouncement($text) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $text    = mysqli_real_escape_string($sqlConnect, $text);
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      if (empty($text)) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "INSERT INTO " . T_ANNOUNCEMENT . " (`text`, `time`, `active`) VALUES ('{$text}', " . time() . ", '1')");
      if ($query) {
            return mysqli_insert_id($sqlConnect);
      }
}
function Wo_GetAnnouncement($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $data    = array();
      if (empty($id) || !is_numeric($id) || $id < 1) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_ANNOUNCEMENT . " WHERE `id` = {$id} ORDER BY `id` DESC");
      if (mysqli_num_rows($query) == 1) {
            $fetched_data         = mysqli_fetch_assoc($query);
            $fetched_data['text'] = Wo_Markup($fetched_data['text']);
            $fetched_data['text'] = Wo_Emo($fetched_data['text']);
            return $fetched_data;
      }
}
function Wo_GetAnnouncementViews($id) {
      global $sqlConnect, $wo;
      $id            = Wo_Secure($id);
      $query_one     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as `count` FROM " . T_ANNOUNCEMENT_VIEWS . " WHERE `announcement_id` = {$id}");
      $sql_query_one = mysqli_fetch_assoc($query_one);
      return $sql_query_one['count'];
}
function Wo_GetActiveAnnouncements() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $data    = array();
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_ANNOUNCEMENT . " WHERE `active` = '1' ORDER BY `id` DESC");
      while ($row = mysqli_fetch_assoc($query)) {
            $data[] = Wo_GetAnnouncement($row['id']);
      }
      return $data;
}
function Wo_GetHomeAnnouncements() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query        = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_ANNOUNCEMENT . " WHERE `active` = '1' AND `id` NOT IN (SELECT `announcement_id` FROM " . T_ANNOUNCEMENT_VIEWS . " WHERE `user_id` = {$user_id}) ORDER BY RAND() LIMIT 1");
      $fetched_data = mysqli_fetch_assoc($query);
      $data         = Wo_GetAnnouncement($fetched_data['id']);
      return $data;
}
function Wo_GetInactiveAnnouncements() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $data    = array();
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_ANNOUNCEMENT . " WHERE `active` = '0' ORDER BY `id` DESC");
      while ($row = mysqli_fetch_assoc($query)) {
            $data[] = Wo_GetAnnouncement($row['id']);
      }
      return $data;
}
function Wo_DeleteAnnouncement($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_ANNOUNCEMENT . " WHERE `id` = {$id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_ANNOUNCEMENT_VIEWS . " WHERE `announcement_id` = {$id}");
      if ($query_one) {
            return true;
      }
}
function Wo_IsActiveAnnouncement($id) {
      global $sqlConnect;
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_ANNOUNCEMENT . " WHERE `id` = '{$id}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_IsViewedAnnouncement($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      $query   = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_ANNOUNCEMENT_VIEWS . " WHERE `announcement_id` = '{$id}' AND `user_id` = '{$user_id}'");
      return (Wo_Sql_Result($query, 0) > 0) ? true : false;
}
function Wo_IsThereAnnouncement() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      $query   = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_ANNOUNCEMENT . " WHERE `active` = '1' AND `id` NOT IN (SELECT `announcement_id` FROM " . T_ANNOUNCEMENT_VIEWS . " WHERE `user_id` = {$user_id})");
      $sql     = mysqli_fetch_assoc($query);
      return ($sql['count'] > 0) ? true : false;
}
function Wo_DisableAnnouncement($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      if (Wo_IsActiveAnnouncement($id) === false) {
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ANNOUNCEMENT . " SET `active` = '0' WHERE `id` = {$id}");
      if ($query_one) {
            return true;
      }
}
function Wo_ActivateAnnouncement($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsAdmin($user_id) === false) {
            return false;
      }
      if (Wo_IsActiveAnnouncement($id) === true) {
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ANNOUNCEMENT . " SET `active` = '1' WHERE `id` = {$id}");
      if ($query_one) {
            return true;
      }
}
function Wo_UpdateAnnouncementViews($id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (Wo_IsActiveAnnouncement($id) === false) {
            return false;
      }
      if (Wo_IsViewedAnnouncement($id) === true) {
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "INSERT INTO " . T_ANNOUNCEMENT_VIEWS . " (`user_id`, `announcement_id`) VALUES ('{$user_id}', '{$id}')");
      if ($query_one) {
            return true;
      }
}
function Wo_RegisterApp($registration_data) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($registration_data)) {
            return false;
      }
      if (empty($registration_data['app_user_id']) || !is_numeric($registration_data['app_user_id']) || $registration_data['app_user_id'] < 1) {
            return false;
      }
      $id_str                          = sha1($registration_data['app_user_id'] . microtime() . time());
      $registration_data['app_id']     = Wo_Secure(substr($id_str, 0, 20));
      $secret_str                      = sha1($registration_data['app_user_id'] . Wo_GenerateKey(55, 55) . microtime());
      $registration_data['app_secret'] = Wo_Secure(substr($secret_str, 0, 39));
      if (empty($registration_data['app_secret']) || empty($registration_data['app_id'])) {
            return false;
      }
      $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
      $data   = '\'' . implode('\', \'', $registration_data) . '\'';
      $query  = mysqli_query($sqlConnect, "INSERT INTO " . T_APPS . " ({$fields}) VALUES ({$data})");
      if ($query) {
            return mysqli_insert_id($sqlConnect);
      }
}
function Wo_IsAppOnwer($app_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id       = Wo_Secure($wo['user']['user_id']);
      $app_id        = Wo_Secure($app_id);
      $query_one     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as `count` FROM " . T_APPS . " WHERE `app_user_id` = {$user_id} AND `id` = {$app_id} AND `active` = '1'");
      $sql_query_one = mysqli_fetch_assoc($query_one);
      return ($sql_query_one['count'] == 1) ? true : false;
}
function Wo_GetAppsData($placement = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data       = array();
      $user_id    = $wo['user']['user_id'];
      $query_text = "SELECT `id` FROM " . T_APPS;
      if ($placement != 'admin') {
            $query_text .= " WHERE `app_user_id` = {$user_id}";
      }
      $query_one = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            if (is_array($fetched_data)) {
                  $data[] = Wo_GetApp($fetched_data['id']);
            }
      }
      return $data;
}
function Wo_GetApp($app_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($app_id) || !is_numeric($app_id) || $app_id < 1) {
            return false;
      }
      $app_id    = Wo_Secure($app_id);
      $query_one = mysqli_query($sqlConnect, "SELECT * FROM " . T_APPS . " WHERE `id` = {$app_id}");
      if (mysqli_num_rows($query_one) == 1) {
            $sql_query_one               = mysqli_fetch_assoc($query_one);
            $sql_query_one['app_onwer']  = Wo_UserData($sql_query_one['app_user_id']);
            $sql_query_one['app_avatar'] = Wo_GetMedia($sql_query_one['app_avatar']);
            return $sql_query_one;
      }
}
function Wo_UpdateAppImage($app_id, $image) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($app_id) || !is_numeric($app_id) || $app_id < 0) {
            return false;
      }
      if (empty($image)) {
            return false;
      }
      $app_id    = Wo_Secure($app_id);
      $query_one = " UPDATE " . T_APPS . " SET `app_avatar` = '{$image}' WHERE `id` = {$app_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($query) {
            return true;
      }
}
function Wo_UpdateAppData($app_id, $update_data) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($app_id) || !is_numeric($app_id) || $app_id < 0) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      $app_id = Wo_Secure($app_id);
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . Wo_Secure($data) . '\'';
      }
      $impload   = implode(', ', $update);
      $query_one = " UPDATE " . T_APPS . " SET {$impload} WHERE `id` = {$app_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetIdFromAppID($app_id) {
      global $sqlConnect;
      if (empty($app_id)) {
            return false;
      }
      $app_id        = Wo_Secure($app_id);
      $query_one     = "SELECT `id` FROM " . T_APPS . " WHERE `app_id` = '{$app_id}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['id'];
      }
}
function Wo_AccessToken($app_id, $app_secret) {
      global $sqlConnect;
      if (empty($app_id)) {
            return false;
      }
      if (empty($app_secret)) {
            return false;
      }
      $app_id        = Wo_Secure($app_id);
      $app_secret    = Wo_Secure($app_secret);
      $query_one     = "SELECT `id` FROM " . T_APPS . " WHERE `app_id` = '{$app_id}' AND `app_secret` = '{$app_secret}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            return true;
      } else {
            return false;
      }
}
function Wo_IsValidApp($app_id) {
      global $sqlConnect;
      if (empty($app_id)) {
            return false;
      }
      $app_id        = Wo_Secure($app_id);
      $query_one     = "SELECT `id` FROM " . T_APPS . " WHERE `app_id` = '{$app_id}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            return true;
      }
}
function Wo_AppHasPermission($user_id, $app_id) {
      global $sqlConnect, $wo;
      if (empty($app_id)) {
            return false;
      }
      $app_id        = Wo_Secure($app_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_APPS_PERMISSION . " WHERE `app_id` = '{$app_id}' AND `user_id` = '{$user_id}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) > 0) {
            return true;
      } else {
            return false;
      }
}
function Wo_AcceptPermissions($app_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $app_id  = Wo_Secure($app_id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (empty($app_id) || empty($user_id)) {
            return false;
      }
      $query_one     = "INSERT INTO " . T_APPS_PERMISSION . " (`user_id`,`app_id`) VALUES ('{$user_id}','{$app_id}')";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if ($sql_query_one) {
            return true;
      }
}
function Wo_GenrateToken($user_id, $app_id) {
      global $sqlConnect, $wo;
      $app_id  = Wo_Secure($app_id);
      $user_id = Wo_Secure($user_id);
      if (empty($app_id) || empty($user_id)) {
            return false;
      }
      $token     = Wo_GenerateKey(100, 100);
      $query_two = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_TOKENS . " WHERE `app_id` = {$app_id} AND `user_id` = {$user_id}");
      if (mysqli_num_rows($query_two) > 0) {
            $query_three = mysqli_query($sqlConnect, "DELETE FROM " . T_TOKENS . " WHERE `app_id` = {$app_id} AND `user_id` = {$user_id}");
      }
      $query_one     = "INSERT INTO " . T_TOKENS . " (`user_id`,`app_id`,`token`,`time`) VALUES ('{$user_id}','{$app_id}','{$token}','" . time() . "')";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if ($sql_query_one) {
            return $token;
      }
}
function Wo_UserIdFromToken($token) {
      global $sqlConnect, $wo;
      if (empty($token)) {
            return false;
      }
      $query_one     = "SELECT `user_id` FROM " . T_TOKENS . " WHERE `token` = '{$token}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_query_two = mysqli_fetch_assoc($sql_query_one);
            return $sql_query_two['user_id'];
      } else {
            return false;
      }
}
function Wo_GetIdFromToken($token) {
      global $sqlConnect, $wo;
      if (empty($token)) {
            return false;
      }
      $query_one     = "SELECT `app_id` FROM " . T_TOKENS . " WHERE `token` = '{$token}'";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_query_two = mysqli_fetch_assoc($sql_query_one);
            return $sql_query_two['app_id'];
      } else {
            return false;
      }
}
function Wo_RegisterPage($registration_data) {
      global $wo, $sqlConnect;
      if (empty($registration_data)) {
            return false;
      }
      $registration_data['registered'] = date('n') . '/' . date("Y");
      $fields                          = '`' . implode('`, `', array_keys($registration_data)) . '`';
      $data                            = '\'' . implode('\', \'', $registration_data) . '\'';
      $query                           = mysqli_query($sqlConnect, "INSERT INTO " . T_PAGES . " ({$fields}) VALUES ({$data})");
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetMyPages() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data       = array();
      $user_id    = Wo_Secure($wo['user']['user_id']);
      $query_text = "SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id}";
      $query_one  = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            if (is_array($fetched_data)) {
                  $data[] = Wo_PageData($fetched_data['page_id']);
            }
      }
      return $data;
}
function Wo_IsPageOnwer($page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id) || $page_id < 0) {
            return false;
      }
      $user_id = Wo_Secure($wo['user']['user_id']);
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $query = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`) FROM " . T_PAGES . " WHERE `page_id` = {$page_id} AND `user_id` = {$user_id} AND `active` = '1'");
      return (Wo_Sql_Result($query, '0') == 1) ? true : false;
}
function Wo_PageExists($page_name = '') {
      global $sqlConnect;
      if (empty($page_name)) {
            return false;
      }
      $page_name = Wo_Secure($page_name);
      $query     = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) FROM " . T_PAGES . " WHERE `page_name`= '{$page_name}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_PageExistsByID($id = 0) {
      global $sqlConnect;
      if (empty($id)) {
            return false;
      }
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) FROM " . T_PAGES . " WHERE `page_id`= '{$id}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_PageIdFromPagename($page_name = '') {
      global $sqlConnect;
      if (empty($page_name)) {
            return false;
      }
      $page_name = Wo_Secure($page_name);
      $query     = mysqli_query($sqlConnect, "SELECT `page_id` FROM " . T_PAGES . " WHERE `page_name` = '{$page_name}'");
      return Wo_Sql_Result($query, 0, 'page_id');
}
function Wo_PageData($page_id = 0) {
      global $wo, $sqlConnect, $cache;
      if (empty($page_id) || !is_numeric($page_id) || $page_id < 0) {
            return false;
      }
      $data           = array();
      $page_id        = Wo_Secure($page_id);
      $query_one      = "SELECT * FROM " . T_PAGES . " WHERE `page_id` = {$page_id}";
      $hashed_page_Id = md5($page_id);
      if ($wo['config']['cacheSystem'] == 1) {
            $fetched_data = $cache->read($hashed_page_Id . '_PAGE_Data.tmp');
            if (empty($fetched_data)) {
                  $sql          = mysqli_query($sqlConnect, $query_one);
                  $fetched_data = mysqli_fetch_assoc($sql);
                  $cache->write($hashed_page_Id . '_PAGE_Data.tmp', $fetched_data);
            }
      } else {
            $sql          = mysqli_query($sqlConnect, $query_one);
            $fetched_data = mysqli_fetch_assoc($sql);
      }
      if (empty($fetched_data)) {
            return array();
      }
      $fetched_data['avatar']   = Wo_GetMedia($fetched_data['avatar']);
      $fetched_data['cover']    = Wo_GetMedia($fetched_data['cover']);
      $fetched_data['about']    = $fetched_data['page_description'];
      $fetched_data['id']       = $fetched_data['page_id'];
      $fetched_data['type']     = 'page';
      $fetched_data['url']      = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['page_name']);
      $fetched_data['name']     = $fetched_data['page_title'];
      $fetched_data['category'] = $wo['page_categories'][$fetched_data['page_category']];
      return $fetched_data;
}
function Wo_PageActive($page_name) {
      global $sqlConnect;
      if (empty($page_name)) {
            return false;
      }
      $page_name = Wo_Secure($page_name);
      $query     = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) FROM " . T_PAGES . "  WHERE `page_name`= '{$page_name}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_GetPagePostPublisherBox($page_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!is_numeric($page_id) or $page_id < 1 or !is_numeric($page_id)) {
            return false;
      }
      if (Wo_IsPageOnwer($page_id)) {
            return Wo_LoadPage('story/publisher-box');
      }
}
function Wo_GetLikeButton($page_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id) or $page_id < 0) {
            return false;
      }
      if (Wo_IsPageOnwer($page_id)) {
            return false;
      }
      $page = $wo['like'] = Wo_PageData($page_id);
      if (!isset($wo['like']['page_id'])) {
            return false;
      }
      $page_id        = Wo_Secure($page_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $like_button    = 'buttons/like';
      $unlike_button  = 'buttons/unlike';
      if (Wo_IsPageLiked($page_id, $logged_user_id) === true) {
            return Wo_LoadPage($unlike_button);
      } else {
            return Wo_LoadPage($like_button);
      }
}
function Wo_IsPageLiked($page_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id) || $page_id < 0) {
            return false;
      }
      if (empty($page_id) || !is_numeric($user_id) || $user_id < 0) {
            $user_id = Wo_Secure($wo['user']['user_id']);
      }
      $query_one = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_PAGES_LIKES . " WHERE `user_id` = '{$user_id}' AND `page_id` = {$page_id} AND `active` = '1'");
      return (Wo_Sql_Result($query_one, 0) == 1) ? true : false;
}
function Wo_RegisterPageLike($page_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($page_id) or empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $page_id    = Wo_Secure($page_id);
      $user_id    = Wo_Secure($user_id);
      $page_onwer = Wo_GetUserIdFromPageId($page_id);
      $active     = 1;
      if (Wo_IsPageLiked($page_id, $user_id) === true) {
            return false;
      }
      $page_data = Wo_PageData($page_id);
      $query     = mysqli_query($sqlConnect, " INSERT INTO " . T_PAGES_LIKES . " (`user_id`,`page_id`,`active`,`time`) VALUES ({$user_id},{$page_id},'1'," . time() . ")");
      if ($query) {
            if (Wo_IsPageInvited($user_id, $page_id) > 0) {
                  foreach (Wo_GetPageInviters($user_id, $page_id) as $user) {
                        $notification_data = array(
                              'recipient_id' => $user['user_id'],
                              'notifier_id' => $user_id,
                              'type' => 'accepted_invite',
                              'url' => 'index.php?tab1=timeline&u=' . $page_data['page_name']
                        );
                        Wo_RegisterNotification($notification_data);
                  }
                  $delete_invite = Wo_DeleteInvites($user_id, $page_id);
            }
            $notification_data = array(
                  'recipient_id' => $page_onwer,
                  'notifier_id' => $user_id,
                  'page_enable' => false,
                  'type' => 'liked_page',
                  'page_id' => $page_id,
                  'url' => 'index.php?tab1=timeline&u=' . $page_data['page_name']
            );
            Wo_RegisterNotification($notification_data);
      }
      return true;
}
function Wo_DeletePageLike($page_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($page_id) or empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $page_id = Wo_Secure($page_id);
      $user_id = Wo_Secure($user_id);
      $active  = 1;
      if (Wo_IsPageLiked($page_id, $user_id) === false) {
            return false;
      }
      $user_data = Wo_UserData($user_id);
      $query     = mysqli_query($sqlConnect, " DELETE FROM " . T_PAGES_LIKES . " WHERE `user_id` = {$user_id} AND `page_id` = '{$page_id}' AND `active` = '1'");
      if ($query) {
            return true;
      }
}
function Wo_UpdatePageData($page_id = 0, $update_data) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id) || $page_id < 0) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      if (isset($update_data['verified'])) {
            if (Wo_IsAdmin($wo['user']['user_id']) === false) {
                  return false;
            }
      }
      $page_id = Wo_Secure($page_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if (Wo_IsPageOnwer($page_id) === false) {
                  return false;
            }
      }
      if (!empty($update_data['page_category'])) {
            if (!array_key_exists($update_data['page_category'], $wo['page_categories'])) {
                  $update_data['page_category'] = 1;
            }
      }
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . Wo_Secure($data) . '\'';
      }
      $impload   = implode(', ', $update);
      $query_one = " UPDATE " . T_PAGES . " SET {$impload} WHERE `page_id` = {$page_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($page_id) . '_PAGE_Data.tmp');
      }
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_UpdatePostData($post_id = 0, $update_data) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 0) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      $post_id = Wo_Secure($post_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if (Wo_IsPostOnwer($post_id, $wo['user']['user_id']) === false) {
                  return false;
            }
      }
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . Wo_Secure($data) . '\'';
      }
      $impload   = implode(', ', $update);
      $query_one = " UPDATE " . T_POSTS . " SET {$impload} WHERE `id` = {$post_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($post_id) . '_P_Data.tmp');
      }
      if ($query) {
            return $post_id;
      } else {
            return false;
      }
}
function Wo_GetPageIdFromPostId($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `page_id` FROM " . T_POSTS . " WHERE `id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['page_id'];
      }
}
function Wo_GetUserIdFromPageId($page_id = 0) {
      global $sqlConnect;
      if (empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      $page_id       = Wo_Secure($page_id);
      $query_one     = "SELECT `user_id` FROM " . T_PAGES . " WHERE `page_id` = {$page_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['user_id'];
      }
}
function Wo_DeletePage($page_id = 0) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id) || $page_id < 1) {
            return false;
      }
      $page_id = Wo_Secure($page_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if (Wo_IsPageOnwer($page_id) === false) {
                  return false;
            }
      }
      $query_one_delete_photos = mysqli_query($sqlConnect, " SELECT `avatar`,`cover` FROM " . T_PAGES . " WHERE `page_id` = {$page_id}");
      $fetched_data            = mysqli_fetch_assoc($query_one_delete_photos);
      if (isset($fetched_data['avatar']) && !empty($fetched_data['avatar']) && $fetched_data['avatar'] != $wo['pageDefaultAvatar']) {
            @unlink($fetched_data['avatar']);
      }
      if (isset($fetched_data['cover']) && !empty($fetched_data['cover']) && $fetched_data['cover'] != $wo['userDefaultCover']) {
            @unlink($fetched_data['cover']);
      }
      $query_two_delete_media = mysqli_query($sqlConnect, " SELECT `postFile` FROM " . T_POSTS . " WHERE `page_id` = {$page_id}");
      if (mysqli_num_rows($query_two_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_two_delete_media)) {
                  if (isset($fetched_data['postFile']) && !empty($fetched_data['postFile'])) {
                        @unlink($fetched_data['postFile']);
                  }
            }
      }
      $query_four_delete_media = mysqli_query($sqlConnect, "SELECT `id`,`post_id` FROM " . T_POSTS . " WHERE `page_id` = {$page_id}");
      if (mysqli_num_rows($query_four_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_four_delete_media)) {
                  $delete_posts = Wo_DeletePost($fetched_data['id']);
            }
      }
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($user_id) . '_PAGE_Data.tmp');
            $query_two = mysqli_query($sqlConnect, "SELECT `id`,`post_id` FROM " . T_POSTS . " WHERE `page_id` = {$page_id}");
            if (mysqli_num_rows($query_two) > 0) {
                  while ($fetched_data_two = mysqli_fetch_assoc($query_two)) {
                        $cache->delete(md5($fetched_data_two['id']) . '_PAGE_Data.tmp');
                        $cache->delete(md5($fetched_data_two['post_id']) . '_PAGE_Data.tmp');
                  }
            }
      }
      $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_PAGES . " WHERE `page_id` = {$page_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_PAGES_LIKES . " WHERE `page_id` = {$page_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `page_id` = {$page_id}");
      if ($query_one) {
            return true;
      }
}
function Wo_CountPageLikes($page_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      $page_id      = Wo_Secure($page_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) AS count FROM " . T_PAGES_LIKES . " WHERE `page_id` = {$page_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_CountPagePosts($page_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      $page_id      = Wo_Secure($page_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_POSTS . " WHERE `page_id` = {$page_id}");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_CountLikesThisWeek($page_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      $time = strtotime("-1 week");
      if (empty($page_id) or !is_numeric($page_id) or $page_id < 1) {
            return false;
      }
      $page_id      = Wo_Secure($page_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) AS count FROM " . T_PAGES_LIKES . " WHERE `page_id` = {$page_id} AND `active` = '1' AND (`time` between {$time} AND " . time() . ")");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_PageSug($limit = 1, $page_id = 0, $type = 'next') {
      global $wo, $sqlConnect;
      if (!is_numeric($limit)) {
            return false;
      }
      $query_not = '';
      if (!is_numeric($page_id) || empty($page_id) || $page_id < 1) {
            $query_not = '';
      }
      if ($type == 'previous') {
            $query_not = "AND `page_id` < $page_id";
      } else {
            $query_not = "AND `page_id` > $page_id";
      }
      $data      = array();
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $query_one = " SELECT `page_id` FROM " . T_PAGES . " WHERE `active` = '1' {$query_not} AND `page_id` NOT IN (SELECT `page_id` FROM " . T_PAGES_LIKES . " WHERE `user_id` = {$user_id} AND `active` = '1') AND `user_id` <> {$user_id}";
      if (isset($limit)) {
            $query_one .= " LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            if (is_array($fetched_data)) {
                  $data[] = Wo_PageData($fetched_data['page_id']);
            }
      }
      return $data;
}
function Wo_GetLikes($user_id = 0, $type = '', $limit = '', $after_user_id = '') {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id       = Wo_Secure($user_id);
      $after_user_id = Wo_Secure($after_user_id);
      $query         = " SELECT `page_id` FROM " . T_PAGES_LIKES . " WHERE `user_id` = {$user_id} AND `active` = '1'";
      if (!empty($after_user_id) && is_numeric($after_user_id)) {
            $query .= " AND `page_id` < {$after_user_id}";
      }
      if ($type == 'sidebar' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY RAND()";
      }
      if ($type == 'profile' && !empty($limit) && is_numeric($limit)) {
            $query .= " ORDER BY `page_id` DESC";
      }
      $query .= " LIMIT {$limit} ";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_PageData($fetched_data['page_id']);
      }
      return $data;
}
function Wo_CountUserLikes($user_id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`page_id`) AS count FROM " . T_PAGES_LIKES . " WHERE `user_id` = {$user_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_GetAllPages($limit = '', $after = '') {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT `page_id` FROM " . T_PAGES;
      if (!empty($after) && is_numeric($after) && $after > 0) {
            $query_one .= " WHERE `page_id` < " . Wo_Secure($after);
      }
      $query_one .= " ORDER BY `page_id` DESC";
      if (isset($limit) and !empty($limit)) {
            $query_one .= " LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $page_data          = Wo_PageData($fetched_data['page_id']);
            $page_data['owner'] = Wo_UserData($page_data['user_id']);
            $data[]             = $page_data;
      }
      return $data;
}
function Wo_RegisterGroup($registration_data) {
      global $wo, $sqlConnect;
      if (empty($registration_data)) {
            return false;
      }
      $registration_data['registered'] = date('n') . '/' . date("Y");
      $fields                          = '`' . implode('`, `', array_keys($registration_data)) . '`';
      $data                            = '\'' . implode('\', \'', $registration_data) . '\'';
      $query                           = mysqli_query($sqlConnect, "INSERT INTO " . T_GROUPS . " ({$fields}) VALUES ({$data})");
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetMyGroups() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data       = array();
      $user_id    = Wo_Secure($wo['user']['user_id']);
      $query_text = "SELECT `id` FROM " . T_GROUPS . " WHERE `user_id` = {$user_id}";
      $query_one  = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            if (is_array($fetched_data)) {
                  $data[] = Wo_GroupData($fetched_data['id']);
            }
      }
      return $data;
}
function Wo_IsGroupOnwer($group_id = 0, $user_id = 0) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id) || $group_id < 0) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            $user_id = Wo_Secure($wo['user']['user_id']);
      }
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            return false;
      }
      $query = mysqli_query($sqlConnect, " SELECT COUNT(`user_id`) FROM " . T_GROUPS . " WHERE `id` = {$group_id} AND `user_id` = {$user_id} AND `active` = '1'");
      return (Wo_Sql_Result($query, '0') == 1) ? true : false;
}
function Wo_GroupExists($group_name = '') {
      global $sqlConnect;
      if (empty($group_name)) {
            return false;
      }
      $group_name = Wo_Secure($group_name);
      $query      = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_GROUPS . " WHERE `group_name`= '{$group_name}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_GroupIdFromGroupname($group_name = '') {
      global $sqlConnect;
      if (empty($group_name)) {
            return false;
      }
      $group_name = Wo_Secure($group_name);
      $query      = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_GROUPS . " WHERE `group_name` = '{$group_name}'");
      return Wo_Sql_Result($query, 0, 'id');
}
function Wo_GroupData($group_id = 0) {
      global $wo, $sqlConnect, $cache;
      if (empty($group_id) || !is_numeric($group_id) || $group_id < 1) {
            return false;
      }
      $data            = array();
      $group_id        = Wo_Secure($group_id);
      $query_one       = "SELECT * FROM " . T_GROUPS . " WHERE `id` = {$group_id}";
      $hashed_group_Id = md5($group_id);
      if ($wo['config']['cacheSystem'] == 1) {
            $fetched_data = $cache->read($hashed_group_Id . '_GROUP_Data.tmp');
            if (empty($fetched_data)) {
                  $sql          = mysqli_query($sqlConnect, $query_one);
                  $fetched_data = mysqli_fetch_assoc($sql);
                  $cache->write($hashed_group_Id . '_GROUP_Data.tmp', $fetched_data);
            }
      } else {
            $sql          = mysqli_query($sqlConnect, $query_one);
            $fetched_data = mysqli_fetch_assoc($sql);
      }
      if (empty($fetched_data)) {
            return array();
      }
      $fetched_data['group_id']    = $fetched_data['id'];
      $fetched_data['avatar']      = Wo_GetMedia($fetched_data['avatar']);
      $fetched_data['cover']       = Wo_GetMedia($fetched_data['cover']);
      $fetched_data['url']         = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['group_name']);
      $fetched_data['name']        = $fetched_data['group_title'];
      $fetched_data['category_id'] = $fetched_data['category'];
      $fetched_data['type']        = 'group';
      $fetched_data['category']    = $wo['page_categories'][$fetched_data['category']];
      return $fetched_data;
}
function Wo_GroupActive($group_name) {
      global $sqlConnect;
      if (empty($group_name)) {
            return false;
      }
      $group_name = Wo_Secure($group_name);
      $query      = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_GROUPS . "  WHERE `group_name` = '{$group_name}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_CanBeOnGroup($group_id) {
      global $sqlConnect;
      if (empty($group_id)) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      if (Wo_IsGroupOnwer($group_id)) {
            return true;
      }
      $group = Wo_GroupData($group_id);
      if (empty($group)) {
            return false;
      }
      if ($group['privacy'] == 1) {
            if (Wo_IsGroupJoined($group_id) === true) {
                  return true;
            }
      } else if ($group['privacy'] == 2) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetGroupPostPublisherBox($group_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!is_numeric($group_id) or $group_id < 1 or !is_numeric($group_id)) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      $continue = false;
      if (Wo_CanBeOnGroup($group_id) === true) {
            $continue = true;
      }
      if ($continue == true) {
            return Wo_LoadPage('story/publisher-box');
      }
}
function Wo_GetJoinButton($group_id = 0) {
      global $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id) or $group_id < 0) {
            return false;
      }
      if (Wo_IsGroupOnwer($group_id)) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      $group    = $wo['join'] = Wo_GroupData($group_id);
      if (!isset($wo['join']['id'])) {
            return false;
      }
      $logged_user_id        = Wo_Secure($wo['user']['user_id']);
      $join_button           = 'buttons/join';
      $leave_button          = 'buttons/leave';
      $accept_request_button = 'buttons/join-requested';
      if (Wo_IsGroupJoined($group_id, $logged_user_id) === true) {
            return Wo_LoadPage($leave_button);
      } else {
            if (Wo_IsJoinRequested($group_id) === true) {
                  return Wo_LoadPage($accept_request_button);
            } else {
                  return Wo_LoadPage($join_button);
            }
      }
}
function Wo_IsGroupJoined($group_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id) || $group_id < 0) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
            $user_id = Wo_Secure($wo['user']['user_id']);
      }
      $group_id  = Wo_Secure($group_id);
      $query_one = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = '{$user_id}' AND `group_id` = {$group_id} AND `active` = '1'");
      return (Wo_Sql_Result($query_one, 0) == 1) ? true : false;
}
function Wo_IsJoinRequested($group_id = 0, $user_id = 0) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id = Wo_Secure($user_id);
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            $user_id = Wo_Secure($wo['user']['user_id']);
      }
      if (!is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id  = Wo_Secure($group_id);
      $query     = "SELECT `id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `user_id` = {$user_id} AND `active` = '0'";
      $sql_query = mysqli_query($sqlConnect, $query);
      if (mysqli_num_rows($sql_query) > 0) {
            return true;
      }
}
function Wo_RegisterGroupJoin($group_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($group_id) or empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $group_id    = Wo_Secure($group_id);
      $user_id     = Wo_Secure($user_id);
      $group_onwer = Wo_GetUserIdFromGroupId($group_id);
      $active      = 1;
      if (Wo_IsGroupJoined($group_id, $user_id) === true) {
            return false;
      }
      $group_data = Wo_GroupData($group_id);
      if ($group_data['join_privacy'] == 2) {
            $active = 0;
      }
      $query = mysqli_query($sqlConnect, " INSERT INTO " . T_GROUP_MEMBERS . " (`user_id`,`group_id`,`active`,`time`) VALUES ({$user_id},{$group_id},'{$active}'," . time() . ")");
      if ($query) {
            if ($active == 1) {
                  $notification_data = array(
                        'recipient_id' => $group_onwer,
                        'notifier_id' => $user_id,
                        'type' => 'joined_group',
                        'group_id' => $group_id,
                        'url' => 'index.php?tab1=timeline&u=' . $group_data['group_name']
                  );
                  Wo_RegisterNotification($notification_data);
            } else if ($active == 0) {
                  $notification_data = array(
                        'recipient_id' => $group_onwer,
                        'notifier_id' => $user_id,
                        'type' => 'requested_to_join_group',
                        'group_id' => $group_id,
                        'url' => 'index.php?tab1=group-setting&group=' . $group_data['group_name'] . '&tab3=requests'
                  );
                  Wo_RegisterNotification($notification_data);
            }
      }
      return true;
}
function Wo_LeaveGroup($group_id = 0, $user_id = 0) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($group_id) or empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      $user_id  = Wo_Secure($user_id);
      $active   = 1;
      if (Wo_IsGroupJoined($group_id, $user_id) === false && Wo_IsJoinRequested($group_id, $user_id) === false) {
            return false;
      }
      $query = mysqli_query($sqlConnect, " DELETE FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = {$user_id} AND `group_id` = '{$group_id}'");
      if ($query) {
            return true;
      }
}
function Wo_UpdateGroupData($group_id = 0, $update_data) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id) || $group_id < 0) {
            return false;
      }
      if (empty($update_data)) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if (Wo_IsGroupOnwer($group_id) === false) {
                  return false;
            }
      }
      if (!empty($update_data['category'])) {
            if (!array_key_exists($update_data['category'], $wo['page_categories'])) {
                  $update_data['category'] = 1;
            }
      }
      $update = array();
      foreach ($update_data as $field => $data) {
            $update[] = '`' . $field . '` = \'' . Wo_Secure($data) . '\'';
      }
      $impload   = implode(', ', $update);
      $query_one = " UPDATE " . T_GROUPS . " SET {$impload} WHERE `id` = {$group_id} ";
      $query     = mysqli_query($sqlConnect, $query_one);
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($group_id) . '_GROUP_Data.tmp');
      }
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_GetGroupIdFromPostId($post_id = 0) {
      global $sqlConnect;
      if (empty($post_id) or !is_numeric($post_id) or $post_id < 1) {
            return false;
      }
      $post_id       = Wo_Secure($post_id);
      $query_one     = "SELECT `group_id` FROM " . T_POSTS . " WHERE `id` = {$post_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['group_id'];
      }
}
function Wo_GetUserIdFromGroupId($group_id = 0) {
      global $sqlConnect;
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id      = Wo_Secure($group_id);
      $query_one     = "SELECT `user_id` FROM " . T_GROUPS . " WHERE `id` = {$group_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['user_id'];
      }
}
function Wo_DeleteGroup($group_id = 0) {
      global $wo, $sqlConnect, $cache;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id) || $group_id < 1) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      if (Wo_IsAdmin($wo['user']['user_id']) === false) {
            if (Wo_IsGroupOnwer($group_id) === false) {
                  return false;
            }
      }
      $query_one_delete_photos = mysqli_query($sqlConnect, " SELECT `avatar`,`cover` FROM " . T_GROUPS . " WHERE `id` = {$group_id}");
      $fetched_data            = mysqli_fetch_assoc($query_one_delete_photos);
      if (isset($fetched_data['avatar']) && !empty($fetched_data['avatar']) && $fetched_data['avatar'] != $wo['groupDefaultAvatar']) {
            @unlink($fetched_data['avatar']);
      }
      if (isset($fetched_data['cover']) && !empty($fetched_data['cover']) && $fetched_data['cover'] != $wo['userDefaultCover']) {
            @unlink($fetched_data['cover']);
      }
      $query_two_delete_media = mysqli_query($sqlConnect, " SELECT `postFile` FROM " . T_POSTS . " WHERE `group_id` = {$group_id}");
      if (mysqli_num_rows($query_two_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_two_delete_media)) {
                  if (isset($fetched_data['postFile']) && !empty($fetched_data['postFile'])) {
                        @unlink($fetched_data['postFile']);
                  }
            }
      }
      $query_four_delete_media = mysqli_query($sqlConnect, "SELECT `id`,`post_id` FROM " . T_POSTS . " WHERE `group_id` = {$group_id}");
      if (mysqli_num_rows($query_four_delete_media) > 0) {
            while ($fetched_data = mysqli_fetch_assoc($query_four_delete_media)) {
                  $delete_posts = Wo_DeletePost($fetched_data['id']);
            }
      }
      if ($wo['config']['cacheSystem'] == 1) {
            $cache->delete(md5($user_id) . '_GROUP_Data.tmp');
            $query_two = mysqli_query($sqlConnect, "SELECT `id`,`post_id` FROM " . T_POSTS . " WHERE `group_id` = {$group_id}");
            if (mysqli_num_rows($query_two) > 0) {
                  while ($fetched_data_two = mysqli_fetch_assoc($query_two)) {
                        $cache->delete(md5($fetched_data_two['id']) . '_GROUP_Data.tmp');
                        $cache->delete(md5($fetched_data_two['post_id']) . '_GROUP_Data.tmp');
                  }
            }
      }
      $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_GROUPS . " WHERE `id` = {$group_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id}");
      $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `group_id` = {$group_id}");
      if ($query_one) {
            return true;
      }
}
function Wo_CountGroupMembers($group_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id     = Wo_Secure($group_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`group_id`) AS count FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_CountGroupPosts($group_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id     = Wo_Secure($group_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_POSTS . " WHERE `group_id` = {$group_id}");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_CountJoinedThisWeek($group_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      $time = strtotime("-1 week");
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id     = Wo_Secure($group_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`group_id`) AS count FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '1' AND (`time` between {$time} AND " . time() . ")");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_GroupSug($limit = 20) {
      global $wo, $sqlConnect;
      if (!is_numeric($limit)) {
            return false;
      }
      $data      = array();
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $query_one = " SELECT `id` FROM " . T_GROUPS . " WHERE `active` = '1' AND `id` NOT IN (SELECT `group_id` FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = {$user_id}) AND `user_id` <> {$user_id}";
      if (isset($limit)) {
            $query_one .= " ORDER BY RAND() LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = Wo_GroupData($fetched_data['id']);
      }
      return $data;
}
function Wo_GetGroupMembers($group_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id  = Wo_Secure($group_id);
      $query     = " SELECT `user_id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '1'";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetUsersGroups($user_id = 0, $limit = 12) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id   = Wo_Secure($user_id);
      $query     = " SELECT `group_id` FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = {$user_id} AND `active` = '1' ORDER BY `id` LIMIT {$limit}";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_GroupData($fetched_data['group_id']);
      }
      return $data;
}
function Wo_GetGroupSettingMembers($group_id = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id  = Wo_Secure($group_id);
      $query     = " SELECT `user_id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '1'";
      $sql_query = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_CountUserGroups($user_id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $user_id      = Wo_Secure($user_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = {$user_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_GetAllGroups($limit = '', $after = '') {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT `id` FROM " . T_GROUPS;
      if (!empty($after) && is_numeric($after) && $after > 0) {
            $query_one .= " WHERE `id` < " . Wo_Secure($after);
      }
      $query_one .= " ORDER BY `id` DESC";
      if (isset($limit) and !empty($limit)) {
            $query_one .= " LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $group_data            = Wo_GroupData($fetched_data['id']);
            $group_data['members'] = Wo_CountGroupMembers($fetched_data['id']);
            $group_data['owner']   = Wo_UserData($group_data['user_id']);
            $data[]                = $group_data;
      }
      return $data;
}
function Wo_GetRegisteredDataStatics($month, $type = 'user') {
      global $wo, $sqlConnect;
      $year       = date("Y");
      $type_table = T_USERS;
      $type_id    = 'user_id';
      if ($type == 'user') {
            $type_table = T_USERS;
            $type_id    = 'user_id';
      } else if ($type == 'page') {
            $type_table = T_PAGES;
            $type_id    = 'page_id';
      } else if ($type == 'group') {
            $type_table = T_GROUPS;
            $type_id    = 'id';
      } else if ($type == 'posts') {
            $type_table = T_POSTS;
            $type_id    = 'id';
      }
      $type_id      = Wo_Secure($type_id);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table} WHERE `registered` = '{$month}/{$year}'");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountAllData($type) {
      global $wo, $sqlConnect;
      $type_table = T_USERS;
      $type_id    = 'user_id';
      if ($type == 'user') {
            $type_table = T_USERS;
            $type_id    = 'user_id';
      } else if ($type == 'page') {
            $type_table = T_PAGES;
            $type_id    = 'page_id';
      } else if ($type == 'group') {
            $type_table = T_GROUPS;
            $type_id    = 'id';
      } else if ($type == 'posts') {
            $type_table = T_POSTS;
            $type_id    = 'id';
      } else if ($type == 'comments') {
            $type_table = T_COMMENTS;
            $type_id    = 'id';
      } else if ($type == 'games') {
            $type_table = T_GAMES;
            $type_id    = 'id';
      } else if ($type == 'messages') {
            $type_table = T_MESSAGES;
            $type_id    = 'id';
      }
      $type_id      = Wo_Secure($type_id);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountOnlineData($type = '') {
      global $wo, $sqlConnect;
      $data         = array();
      $type_table   = T_USERS;
      $type_id      = Wo_Secure('user_id');
      $time         = time() - 60;
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`{$type_id}`) as count FROM {$type_table} WHERE `lastseen` > {$time}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_GetBanned($type = '') {
      global $sqlConnect;
      $data  = array();
      $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_BANNED_IPS . " ORDER BY id DESC");
      if ($type == 'user') {
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = $fetched_data['ip_address'];
            }
      } else {
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = $fetched_data;
            }
      }
      return $data;
}
function Wo_BanNewIp($ip) {
      global $sqlConnect;
      $ip           = Wo_Secure($ip);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_BANNED_IPS . " WHERE `ip_address` = '{$ip}'");
      $fetched_data = mysqli_fetch_assoc($query_one);
      if ($fetched_data['count'] > 0) {
            return false;
      }
      $time      = time();
      $query_two = mysqli_query($sqlConnect, "INSERT INTO " . T_BANNED_IPS . " (`ip_address`,`time`) VALUES ('{$ip}','{$time}')");
      if ($query_two) {
            return true;
      }
}
function Wo_IsIpBanned($id) {
      global $sqlConnect;
      $id           = Wo_Secure($id);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_BANNED_IPS . " WHERE `id` = '{$id}'");
      $fetched_data = mysqli_fetch_assoc($query_one);
      if ($fetched_data['count'] > 0) {
            return true;
      } else {
            return false;
      }
}
function Wo_DeleteBanned($id) {
      global $sqlConnect;
      $id = Wo_Secure($id);
      if (Wo_IsIpBanned($id) === false) {
            return false;
      }
      $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_BANNED_IPS . " WHERE `id` = {$id}");
      if ($query_two) {
            return true;
      }
}
function Wo_GameExists($id) {
      global $sqlConnect;
      if (empty($id)) {
            return false;
      }
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_GAMES . " WHERE `id` = '{$id}'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_GameData($game_id) {
      global $wo, $sqlConnect, $cache;
      if (empty($game_id) || !is_numeric($game_id) || $game_id < 1) {
            return false;
      }
      $data           = array();
      $game_id        = Wo_Secure($game_id);
      $query_one      = "SELECT * FROM " . T_GAMES . " WHERE `id` = {$game_id}";
      $hashed_game_id = md5($game_id);
      if ($wo['config']['cacheSystem'] == 1) {
            $fetched_data = $cache->read($hashed_game_id . '_GAME_Data.tmp');
            if (empty($fetched_data)) {
                  $sql          = mysqli_query($sqlConnect, $query_one);
                  $fetched_data = mysqli_fetch_assoc($sql);
                  $cache->write($hashed_game_id . '_GAME_Data.tmp', $fetched_data);
            }
      } else {
            $sql          = mysqli_query($sqlConnect, $query_one);
            $fetched_data = mysqli_fetch_assoc($sql);
      }
      if (empty($fetched_data)) {
            return array();
      }
      $fetched_data['game_avatar'] = Wo_GetMedia($fetched_data['game_avatar']);
      $fetched_data['url']         = Wo_SeoLink('index.php?tab1=game&id=' . $fetched_data['id']);
      $fetched_data['name']        = $fetched_data['game_name'];
      $fetched_data['last_play']   = Wo_LastPlay($fetched_data['id']);
      return $fetched_data;
}
function Wo_LastPlay($id) {
      global $wo, $sqlConnect;
      $data = array();
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      $id           = Wo_Secure($id);
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query        = mysqli_query($sqlConnect, "SELECT `last_play` FROM " . T_GAMES_PLAYERS . " WHERE `game_id` = {$id} AND `user_id` = {$user_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['last_play'];
}
function Wo_GetAllGames($limit = 5, $after = 0) {
      global $wo, $sqlConnect;
      $data      = array();
      $query_one = " SELECT `id` FROM " . T_GAMES;
      if (!empty($after) && is_numeric($after) && $after > 0) {
            $query_one .= " WHERE `id` < " . Wo_Secure($after);
      }
      $query_one .= " ORDER BY `id` DESC";
      if (isset($limit) and !empty($limit)) {
            $query_one .= " LIMIT {$limit}";
      }
      $sql = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $fetched_data            = Wo_GameData($fetched_data['id']);
            $fetched_data['players'] = Wo_CountGamePlayers($fetched_data['id']);
            $data[]                  = $fetched_data;
      }
      return $data;
}
function Wo_AddGame($data = array()) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($data)) {
            return false;
      }
      $fields = '`' . implode('`, `', array_keys($data)) . '`';
      $data   = '\'' . implode('\', \'', $data) . '\'';
      $query  = mysqli_query($sqlConnect, "INSERT INTO " . T_GAMES . " ({$fields}) VALUES ({$data})");
      if ($query) {
            return true;
      } else {
            return false;
      }
}
function Wo_IsPlayingGame($id) {
      global $wo, $sqlConnect;
      $data = array();
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      $id           = Wo_Secure($id);
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_GAMES_PLAYERS . " WHERE `game_id` = {$id} AND `user_id` = {$user_id} AND `active` = '1' ");
      $fetched_data = mysqli_fetch_assoc($query);
      if ($fetched_data['count'] > 0) {
            return true;
      }
}
function Wo_AddPlayGame($id) {
      global $wo, $sqlConnect;
      $data = array();
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      $id      = Wo_Secure($id);
      $user_id = Wo_Secure($wo['user']['user_id']);
      $time    = time();
      if (Wo_IsPlayingGame($id) === true) {
            $query_one = mysqli_query($sqlConnect, "UPDATE " . T_GAMES_PLAYERS . " set `last_play` = {$time} WHERE `game_id` = {$id} AND `user_id` = {$user_id}");
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "INSERT INTO " . T_GAMES_PLAYERS . " (`game_id`, `user_id`, `active`, `last_play`) VALUES ({$id}, {$user_id}, '1', {$time})");
      if ($query_one) {
            return true;
      }
}
function Wo_CountGamePlayers($id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      $id           = Wo_Secure($id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_GAMES_PLAYERS . " WHERE `game_id` = {$id} AND `active` = '1'");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_GetMyGames() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $data       = array();
      $user_id    = Wo_Secure($wo['user']['user_id']);
      $query_text = "SELECT `game_id` FROM " . T_GAMES_PLAYERS . " WHERE `user_id` = {$user_id} ORDER BY `last_play` DESC";
      $query_one  = mysqli_query($sqlConnect, $query_text);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            if (is_array($fetched_data)) {
                  $data[] = Wo_GameData($fetched_data['game_id']);
            }
      }
      return $data;
}
function Wo_IsNameExist($username, $active = 0) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($username)) {
            return false;
      }
      $active_text = '';
      if ($active == 1) {
            $active_text = "AND `active` = '1'";
      }
      $username     = Wo_Secure($username);
      $query_text   = "SELECT (SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `username` = '{$username}' {$active_text}) as users, (SELECT COUNT(`page_id`) FROM " . T_PAGES . " WHERE `page_name` = '{$username}' {$active_text}) as pages, (SELECT COUNT(`id`) FROM " . T_GROUPS . " WHERE `group_name` = '{$username}' {$active_text}) as groups";
      $query        = mysqli_query($sqlConnect, $query_text);
      $fetched_data = mysqli_fetch_assoc($query);
      if ($fetched_data['users'] == 1) {
            return array(
                  true,
                  'type' => 'user'
            );
      } else if ($fetched_data['pages'] == 1) {
            return array(
                  true,
                  'type' => 'page'
            );
      } else if ($fetched_data['groups'] == 1) {
            return array(
                  true,
                  'type' => 'group'
            );
      } else {
            return array(
                  false
            );
      }
}
function Wo_GetGroupRequests($group_id) {
      global $wo, $sqlConnect;
      $data      = array();
      $group_id  = Wo_Secure($group_id);
      $query_one = " SELECT `user_id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '0' ORDER BY `id` DESC";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_AcceptJoinRequest($user_id, $group_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      if (!isset($group_id) or empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      $user_id  = Wo_Secure($user_id);
      if (Wo_IsGroupOnwer($group_id) === false) {
            return false;
      }
      if (Wo_IsJoinRequested($group_id, $user_id) === false) {
            return false;
      }
      if (Wo_IsGroupJoined($group_id, $user_id) === true) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "UPDATE " . T_GROUP_MEMBERS . " SET `active` = '1' WHERE `user_id` = {$user_id} AND `group_id` = {$group_id} AND `active` = '0'");
      if ($query) {
            $group                   = Wo_GroupData($group_id);
            $notification_data_array = array(
                  'recipient_id' => $user_id,
                  'notifier_id' => $group['user_id'],
                  'type' => 'accepted_join_request',
                  'url' => 'index.php?tab1=timeline&u=' . $group['group_name']
            );
            Wo_RegisterNotification($notification_data_array);
            return true;
      }
}
function Wo_DeleteJoinRequest($user_id, $group_id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($user_id) or empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      if (!isset($group_id) or empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id = Wo_Secure($group_id);
      $user_id  = Wo_Secure($user_id);
      if (Wo_IsGroupOnwer($group_id) === false) {
            return false;
      }
      if (Wo_IsJoinRequested($group_id, $user_id) === false) {
            return false;
      }
      if (Wo_IsGroupJoined($group_id, $user_id) === true) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "DELETE FROM " . T_GROUP_MEMBERS . " WHERE `user_id` = {$user_id} AND `group_id` = {$group_id} AND `active` = '0'");
      if ($query) {
            return true;
      }
}
function Wo_CountGroupRequests($group_id) {
      global $wo, $sqlConnect;
      $data = array();
      if (empty($group_id) or !is_numeric($group_id) or $group_id < 1) {
            return false;
      }
      $group_id     = Wo_Secure($group_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS count FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id} AND `active` = '0'");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['count'];
}
function Wo_RegisterAlbumMedia($id, $media) {
      global $wo, $sqlConnect;
      if (empty($id) or !is_numeric($id) or $id < 1) {
            return false;
      }
      if (empty($media)) {
            return false;
      }
      $query_one = mysqli_query($sqlConnect, "INSERT INTO " . T_ALBUMS_MEDIA . " (`post_id`,`image`) VALUES ({$id}, '{$media}')");
      if ($query_one) {
            return true;
      }
}
function Wo_GetAlbumPhotos($post_id) {
      global $wo, $sqlConnect;
      $data      = array();
      $post_id   = Wo_Secure($post_id);
      $query_one = "SELECT `id`,`image`,`post_id` FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id} ORDER BY `id` DESC";
      $sql       = mysqli_query($sqlConnect, $query_one);
      while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $explode2                  = @end(explode('.', $fetched_data['image']));
            $explode3                  = @explode('.', $fetched_data['image']);
            $fetched_data['image_org'] = $explode3[0] . '_small.' . $explode2;
            $fetched_data['image']     = Wo_GetMedia($fetched_data['image']);
            $data[]                    = $fetched_data;
      }
      return $data;
}
function Wo_CountAlbumImages($post_id) {
      global $wo, $sqlConnect;
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      $post_id      = Wo_Secure($post_id);
      $query_one    = "SELECT COUNT(`id`) as count FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id} ORDER BY `id` DESC";
      $sql          = mysqli_query($sqlConnect, $query_one);
      $fetched_data = mysqli_fetch_assoc($sql);
      return $fetched_data['count'];
}
function Wo_CountUserAlbums($user_id) {
      global $wo, $sqlConnect;
      if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
            return false;
      }
      $user_id      = Wo_Secure($user_id);
      $query_one    = "SELECT COUNT(`id`) as count FROM " . T_POSTS . " WHERE `user_id` = {$user_id} AND `album_name` <> '' ORDER BY `id` DESC";
      $sql          = mysqli_query($sqlConnect, $query_one);
      $fetched_data = mysqli_fetch_assoc($sql);
      return $fetched_data['count'];
}
function Wo_DeleteImageFromAlbum($post_id, $id) {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($post_id) || !is_numeric($post_id) || $post_id < 1) {
            return false;
      }
      if (empty($id) || !is_numeric($id) || $id < 1) {
            return false;
      }
      if (Wo_IsPostOnwer($post_id, $wo['user']['user_id']) === false) {
            return false;
      }
      $id           = Wo_Secure($id);
      $post_id      = Wo_Secure($post_id);
      $delete_query = mysqli_query($sqlConnect, "DELETE FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id} AND `id` = {$id}");
      if ($delete_query) {
            $delete_query_2 = mysqli_query($sqlConnect, "SELECT `post_id` FROM " . T_ALBUMS_MEDIA . " WHERE `post_id` = {$post_id}");
            if (mysqli_num_rows($delete_query_2) == 0) {
                  $delete_post = Wo_DeletePost($post_id);
            }
            return true;
      }
}
function Wo_AlbumImageData($data = array()) {
      global $wo, $sqlConnect;
      if (!empty($data['id'])) {
            $id = Wo_Secure($data['id']);
      }
      $order_by = '';
      if (!empty($data['after_image_id']) && is_numeric($data['after_image_id'])) {
            $data['after_image_id'] = Wo_Secure($data['after_image_id']);
            $subquery               = " `id` <> " . $data['after_image_id'] . " AND `id` < " . $data['after_image_id'];
            $order_by               = 'DESC';
      } else if (!empty($data['before_image_id']) && is_numeric($data['before_image_id'])) {
            $data['before_image_id'] = Wo_Secure($data['before_image_id']);
            $subquery                = " `id` <> " . $data['before_image_id'] . " AND `id` > " . $data['before_image_id'];
            $order_by                = 'ASC';
      } else {
            $subquery = " `id` = {$id}";
      }
      if (!empty($data['post_id']) && is_numeric($data['post_id'])) {
            $data['post_id'] = Wo_Secure($data['post_id']);
            $subquery .= " AND `post_id` = " . $data['post_id'];
      }
      $query_one    = "SELECT * FROM " . T_ALBUMS_MEDIA . " WHERE $subquery ORDER by `id` {$order_by}";
      $sql          = mysqli_query($sqlConnect, $query_one);
      $fetched_data = mysqli_fetch_assoc($sql);
      if (!empty($fetched_data)) {
            $fetched_data['image_org'] = Wo_GetMedia($fetched_data['image']);
      }
      return $fetched_data;
}
function Wo_GetCommentReplies($comment_id = 0, $limit = 5, $order_by = 'ASC') {
      global $sqlConnect;
      if (empty($comment_id) || !is_numeric($comment_id) || $comment_id < 0) {
            return false;
      }
      $comment_id = Wo_Secure($comment_id);
      $data       = array();
      $query      = "SELECT `id` FROM " . T_COMMENTS_REPLIES . " WHERE `comment_id` = {$comment_id} ORDER BY `id` {$order_by}";
      if (($comments_num = Wo_CountCommentReplies($comment_id)) > $limit) {
            $query .= " LIMIT " . ($comments_num - $limit) . ", {$limit} ";
      }
      $query_one = mysqli_query($sqlConnect, $query);
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $data[] = Wo_GetCommentReply($fetched_data['id']);
      }
      return $data;
}
function Wo_GetCommentReply($reply_id = 0) {
      global $wo, $sqlConnect;
      if (empty($reply_id) || !is_numeric($reply_id) || $reply_id < 0) {
            return false;
      }
      $reply_id     = Wo_Secure($reply_id);
      $query_one    = mysqli_query($sqlConnect, "SELECT * FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$reply_id} ");
      $fetched_data = mysqli_fetch_assoc($query_one);
      if (!empty($fetched_data['page_id'])) {
            $fetched_data['publisher'] = Wo_PageData($fetched_data['page_id']);
            $fetched_data['url']       = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['publisher']['page_name']);
      } else {
            $fetched_data['publisher'] = Wo_UserData($fetched_data['user_id']);
            $fetched_data['url']       = Wo_SeoLink('index.php?tab1=timeline&u=' . $fetched_data['publisher']['username']);
      }
      $fetched_data['Orginaltext']         = Wo_EditMarkup($fetched_data['text']);
      $fetched_data['Orginaltext']         = str_replace('<br>', "\n", $fetched_data['Orginaltext']);
      $fetched_data['text']                = Wo_Markup($fetched_data['text']);
      $fetched_data['text']                = Wo_Emo($fetched_data['text']);
      $fetched_data['onwer']               = false;
      $fetched_data['post_onwer']          = false;
      $fetched_data['comment_likes']       = Wo_CountCommentReplyLikes($fetched_data['id']);
      $fetched_data['comment_wonders']     = Wo_CountCommentReplyWonders($fetched_data['id']);
      $fetched_data['is_comment_wondered'] = false;
      $fetched_data['is_comment_liked']    = false;
      if (Wo_IsLogged() === true) {
            $fetched_data['onwer']               = ($fetched_data['publisher']['user_id'] == $wo['user']['user_id']) ? true : false;
            $fetched_data['is_comment_wondered'] = (Wo_IsCommentReplyWondered($fetched_data['id'], $wo['user']['user_id'])) ? true : false;
            $fetched_data['is_comment_liked']    = (Wo_IsCommentReplyLiked($fetched_data['id'], $wo['user']['user_id'])) ? true : false;
      }
      return $fetched_data;
}
function Wo_CountCommentReplies($comment_id = '') {
      global $sqlConnect;
      if (empty($comment_id) || !is_numeric($comment_id) || $comment_id < 0) {
            return false;
      }
      $comment_id   = Wo_Secure($comment_id);
      $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) AS `replies` FROM " . T_COMMENTS_REPLIES . " WHERE `comment_id` = {$comment_id} ");
      $fetched_data = mysqli_fetch_assoc($query);
      return $fetched_data['replies'];
}
function Wo_DeleteCommentReply($comment_id = '') {
      global $wo, $sqlConnect;
      if ($comment_id < 0 || empty($comment_id) || !is_numeric($comment_id)) {
            return false;
      }
      if (Wo_IsLogged() === false) {
            return false;
      }
      $comment_id   = Wo_Secure($comment_id);
      $query_delete = mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$comment_id}");
      $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENT_REPLIES_WONDERS . " WHERE `reply_id` = {$comment_id}");
      $query_delete .= mysqli_query($sqlConnect, "DELETE FROM " . T_COMMENT_REPLIES_LIKES . " WHERE `reply_id` = {$comment_id}");
      if ($query_delete) {
            return true;
      }
}
function Wo_RegisterCommentReply($data = array()) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($data['comment_id']) || !is_numeric($data['comment_id']) || $data['comment_id'] < 0) {
            return false;
      }
      if (empty($data['text'])) {
            return false;
      }
      if (empty($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] < 0) {
            return false;
      }
      if (!empty($data['page_id'])) {
            if (Wo_IsPageOnwer($data['page_id']) === false) {
                  $data['page_id'] = 0;
            }
      }
      if (!empty($data['text'])) {
            if ($wo['config']['maxCharacters'] > 0) {
                  if (strlen($data['text']) > $wo['config']['maxCharacters']) {
                        return false;
                  }
            }
            $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
            $i          = 0;
            preg_match_all($link_regex, $data['text'], $matches);
            foreach ($matches[0] as $match) {
                  $match_url    = strip_tags($match);
                  $syntax       = '[a]' . urlencode($match_url) . '[/a]';
                  $data['text'] = str_replace($match, $syntax, $data['text']);
            }
            $mention_regex = '/@([A-Za-z0-9_]+)/i';
            preg_match_all($mention_regex, $data['text'], $matches);
            foreach ($matches[1] as $match) {
                  $match         = Wo_Secure($match);
                  $match_user    = Wo_UserData(Wo_UserIdFromUsername($match));
                  $match_search  = '@' . $match;
                  $match_replace = '@[' . $match_user['user_id'] . ']';
                  if (isset($match_user['user_id'])) {
                        $data['text'] = str_replace($match_search, $match_replace, $data['text']);
                        $mentions[]   = $match_user['user_id'];
                  }
            }
      }
      $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
      preg_match_all($hashtag_regex, $data['text'], $matches);
      foreach ($matches[1] as $match) {
            if (!is_numeric($match)) {
                  $hashdata = Wo_GetHashtag($match);
                  if (is_array($hashdata)) {
                        $match_search      = '#' . $match;
                        $match_replace     = '#[' . $hashdata['id'] . ']';
                        $data['text']      = str_replace($match_search, $match_replace, $data['text']);
                        $hashtag_query     = "UPDATE " . T_HASHTAGS . " SET `last_trend_time` = " . time() . ", `trend_use_num` = " . ($hashdata['trend_use_num'] + 1) . " WHERE `id` = " . $hashdata['id'];
                        $hashtag_sql_query = mysqli_query($sqlConnect, $hashtag_query);
                  }
            }
      }
      $comment = Wo_GetPostComment($data['comment_id']);
      $text    = '';
      $type2   = '';
      $page_id = '';
      if (!empty($data['page_id']) && $data['page_id'] > 0) {
            $page_id = $data['page_id'];
      }
      if (isset($comment['text']) && !empty($comment['text'])) {
            $text = substr($comment['text'], 0, 10) . '..';
      }
      $user_id = Wo_GetUserIdFromCommentId($data['comment_id']);
      if (empty($user_id)) {
            $user_id = Wo_GetUserIdFromPageId($comment['page_id']);
            if (empty($user_id)) {
                  return false;
            }
      }
      if (!empty($page_id)) {
            $user_id = '';
      }
      $fields       = '`' . implode('`, `', array_keys($data)) . '`';
      $comment_data = '\'' . implode('\', \'', $data) . '\'';
      $query        = mysqli_query($sqlConnect, "INSERT INTO  " . T_COMMENTS_REPLIES . " ({$fields}) VALUES ({$comment_data})");
      if ($query) {
            $inserted_reply_id       = mysqli_insert_id($sqlConnect);
            $notification_data_array = array(
                  'recipient_id' => $user_id,
                  'page_id' => $page_id,
                  'type' => 'comment_reply',
                  'text' => $text,
                  'type2' => $type2,
                  'url' => 'index.php?tab1=post&id=' . $comment['post_id'] . '&ref=' . $comment['id']
            );
            Wo_RegisterNotification($notification_data_array);
            if (isset($mentions) && is_array($mentions)) {
                  foreach ($mentions as $mention) {
                        $notification_data_array = array(
                              'recipient_id' => $mention,
                              'type' => 'comment_reply_mention',
                              'text' => $text,
                              'page_id' => $page_id,
                              'url' => 'index.php?tab1=post&id=' . $comment['post_id'] . '&ref=' . $comment['id']
                        );
                        Wo_RegisterNotification($notification_data_array);
                  }
            }
            $also = array();
            if (!empty($user_id)) {
                  if (Wo_IsCommentOnwer($data['user_id'], $data['comment_id'])) {
                        $also = Wo_GetRepliedUsers($data['comment_id']);
                  }
                  if (isset($also) && is_array($also)) {
                        foreach ($also as $user) {
                              $notification_data_array = array(
                                    'recipient_id' => $user['user_id'],
                                    'type' => 'also_replied',
                                    'text' => $text,
                                    'url' => 'index.php?tab1=post&id=' . $comment['post_id'] . '&ref=' . $comment['id']
                              );
                              Wo_RegisterNotification($notification_data_array);
                        }
                  }
            }
            return $inserted_reply_id;
      }
}
function Wo_IsCommentOnwer($user_id, $comment_id) {
      global $sqlConnect;
      if (empty($comment_id) or !is_numeric($comment_id) or $comment_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $comment_id    = Wo_Secure($comment_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_COMMENTS . " WHERE `id` = {$comment_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_GetRepliedUsers($comment_id) {
      global $sqlConnect, $wo;
      $data = array();
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($comment_id) || !is_numeric($comment_id) || $comment_id < 1) {
            return false;
      }
      $query = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_COMMENTS_REPLIES . " WHERE `comment_id` = {$comment_id}");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = Wo_UserData($fetched_data['user_id']);
      }
      return $data;
}
function Wo_GetUserProfilePicture($image = '', $type = '') {
      global $sqlConnect, $wo;
      if (empty($image)) {
            return false;
      }
      $explode2  = @end(explode('.', $image));
      $explode3  = @explode('.', $image);
      $image     = $explode3[0] . '_full.' . $explode2;
      $query_one = "SELECT `post_id` FROM " . T_POSTS . " WHERE `postFile` = '{$image}'";
      $query     = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($query) > 0) {
            $fetched_data = mysqli_fetch_assoc($query);
            return $fetched_data['post_id'];
      }
}
function Wo_RegsiterRecent($id = 0, $type = '') {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($id) || !is_numeric($id) || $id < 1) {
            return false;
      }
      if (empty($type)) {
            return false;
      }
      $id   = Wo_Secure($id);
      $type = Wo_Secure($type);
      if ($type == 'timeline') {
            $type = 'user';
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query_delete = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_RECENT_SEARCHES . " WHERE `user_id` = {$user_id} AND `search_id` = '{$id}' AND `search_type` = '{$type}'");
      if (mysqli_num_rows($query_delete) > 0) {
            $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_RECENT_SEARCHES . " WHERE `user_id` = {$user_id} AND `search_id` = '{$id}' AND `search_type` = '{$type}'");
      }
      $query_one = mysqli_query($sqlConnect, "INSERT INTO " . T_RECENT_SEARCHES . " (`user_id`,`search_id`,`search_type`) VALUES ('{$user_id}', '{$id}', '{$type}')");
      if ($query_one) {
            return $id;
      }
}
function Wo_ClearRecent() {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query_delete = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_RECENT_SEARCHES . " WHERE `user_id` = {$user_id}");
      if (mysqli_num_rows($query_delete) > 0) {
            $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_RECENT_SEARCHES . " WHERE `user_id` = {$user_id}");
            if ($query_two) {
                  return true;
            }
      }
}
function Wo_GetSearchAdv($search_qeury, $type) {
      global $sqlConnect;
      $search_qeury = Wo_Secure($search_qeury);
      $data         = array();
      if ($type == 'groups') {
            $query = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_GROUPS . " WHERE ((`group_name` LIKE '%$search_qeury%') OR `group_title` LIKE '%$search_qeury%') AND `active` = '1' LIMIT 50");
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = Wo_GroupData($fetched_data['id']);
            }
      } elseif ($type == 'pages') {
            $query = mysqli_query($sqlConnect, " SELECT `page_id` FROM " . T_PAGES . " WHERE ((`page_name` LIKE '%$search_qeury%') OR `page_title` LIKE '%$search_qeury%') AND `active` = '1' LIMIT 50");
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = Wo_PageData($fetched_data['page_id']);
            }
      } elseif ($type == 'games') {
            $query = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_GAMES . " WHERE `game_name` LIKE '%$search_qeury%' AND `active` = '1' LIMIT 50");
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = Wo_GameData($fetched_data['id']);
            }
      } elseif ($type == 'posts') {
            $query = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_POSTS . " WHERE `postText` LIKE '%$search_qeury%' LIMIT 50");
            while ($fetched_data = mysqli_fetch_assoc($query)) {
                  $data[] = Wo_PostData($fetched_data['id']);
            }
      }
      return $data;
}
function Wo_GetUserAlbums($user_id, $placement = '', $limit = 5000) {
      global $sqlConnect, $wo;
      $data    = array();
      $user_id = Wo_Secure($user_id);
      $query   = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_POSTS . " WHERE `album_name` <> '' AND `user_id` = {$user_id} ORDER BY `id` DESC LIMIT {$limit}");
      while ($fetched_data = mysqli_fetch_assoc($query)) {
            $fetched_data = Wo_PostData($fetched_data['id']);
            if (!empty($fetched_data['photo_album'])) {
                  foreach ($fetched_data['photo_album'] as $id => $photo) {
                        $album = Wo_GetMedia($photo['image_org']);
                  }
                  $fetched_data['first_image'] = $album;
                  $data[]                      = $fetched_data;
            }
      }
      return $data;
}
function Wo_AddCommentReplyWonders($reply_id, $text = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($reply_id) or empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id        = Wo_Secure($reply_id);
      $user_id         = Wo_Secure($wo['user']['user_id']);
      $comment_user_id = Wo_GetUserIdFromReplyId($reply_id);
      $comment         = Wo_GetCommentIdFromReplyId($reply_id);
      $post_id         = Wo_GetPostIdFromCommentId($comment);
      $page_id         = '';
      $post_data       = Wo_PostData($post_id);
      if (!empty($post_data['page_id'])) {
            $page_id = $post_data['page_id'];
      }
      if (Wo_IsPageOnwer($post_data['page_id']) === false) {
            $page_id = 0;
      }
      if (empty($comment_user_id)) {
            return false;
      }
      if (isset($text) && !empty($text)) {
            $text = substr($text, 0, 10) . '..';
      }
      $text = Wo_Secure($text);
      if (Wo_IsCommentReplyWondered($reply_id, $wo['user']['user_id']) === true) {
            $query_one = "DELETE FROM " . T_COMMENT_REPLIES_WONDERS . " WHERE `reply_id` = {$reply_id} AND `user_id` = {$user_id}";
            mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$comment_user_id} AND `type` = 'wondered_reply_comment'");
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unwonder';
            }
      } else {
            $query_two     = "INSERT INTO " . T_COMMENT_REPLIES_WONDERS . " (`user_id`, `reply_id`) VALUES ({$user_id}, {$reply_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  $notification_data_array = array(
                        'recipient_id' => $comment_user_id,
                        'post_id' => $post_id,
                        'type' => 'wondered_reply_comment',
                        'text' => $text,
                        'page_id' => $page_id,
                        'url' => 'index.php?tab1=post&id=' . $post_id . '&ref=' . $comment
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'wonder';
            }
      }
}
function Wo_CountCommentReplyWonders($reply_id) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $query_one     = "SELECT COUNT(`id`) AS `likes` FROM " . T_COMMENT_REPLIES_WONDERS . " WHERE `reply_id` = {$reply_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['likes'];
      }
}
function Wo_IsCommentReplyWondered($reply_id, $user_id) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_COMMENT_REPLIES_WONDERS . " WHERE `reply_id` = {$reply_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_GetCommentIdFromReplyId($reply_id = 0) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $query_one     = "SELECT `comment_id` FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$reply_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['comment_id'];
      }
}
function Wo_GetUserIdFromReplyId($reply_id = 0) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $query_one     = "SELECT `user_id` FROM " . T_COMMENTS_REPLIES . " WHERE `id` = {$reply_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['user_id'];
      }
}
function Wo_AddCommentReplyLikes($reply_id, $text = '') {
      global $wo, $sqlConnect;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (!isset($reply_id) or empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id        = Wo_Secure($reply_id);
      $user_id         = Wo_Secure($wo['user']['user_id']);
      $comment_user_id = Wo_GetUserIdFromReplyId($reply_id);
      $comment         = Wo_GetCommentIdFromReplyId($reply_id);
      $post_id         = Wo_GetPostIdFromCommentId($comment);
      $page_id         = '';
      $post_data       = Wo_PostData($post_id);
      if (!empty($post_data['page_id'])) {
            $page_id = $post_data['page_id'];
      }
      if (Wo_IsPageOnwer($post_data['page_id']) === false) {
            $page_id = 0;
      }
      if (empty($comment_user_id)) {
            return false;
      }
      if (isset($text) && !empty($text)) {
            $text = substr($text, 0, 10) . '..';
      }
      $text = Wo_Secure($text);
      if (Wo_IsCommentReplyLiked($reply_id, $user_id) === true) {
            $query_one = "DELETE FROM " . T_COMMENT_REPLIES_LIKES . " WHERE `reply_id` = {$reply_id} AND `user_id` = {$user_id}";
            mysqli_query($sqlConnect, "DELETE FROM " . T_NOTIFICATION . " WHERE `post_id` = {$post_id} AND `recipient_id` = {$comment_user_id} AND `type` = 'liked_reply_comment'");
            $sql_query_one = mysqli_query($sqlConnect, $query_one);
            if ($sql_query_one) {
                  return 'unliked';
            }
      } else {
            $query_two     = "INSERT INTO " . T_COMMENT_REPLIES_LIKES . " (`user_id`, `reply_id`) VALUES ({$user_id},{$reply_id})";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                  $notification_data_array = array(
                        'recipient_id' => $comment_user_id,
                        'post_id' => $post_id,
                        'type' => 'liked_reply_comment',
                        'text' => $text,
                        'page_id' => $page_id,
                        'url' => 'index.php?tab1=post&id=' . $post_id . '&ref=' . $comment
                  );
                  Wo_RegisterNotification($notification_data_array);
                  return 'liked';
            }
      }
}
function Wo_CountCommentReplyLikes($reply_id) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $query_one     = "SELECT COUNT(`id`) AS `likes` FROM " . T_COMMENT_REPLIES_LIKES . " WHERE `reply_id` = {$reply_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) == 1) {
            $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
            return $sql_fetch_one['likes'];
      }
}
function Wo_IsCommentReplyLiked($reply_id, $user_id) {
      global $sqlConnect;
      if (empty($reply_id) or !is_numeric($reply_id) or $reply_id < 1) {
            return false;
      }
      if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
            return false;
      }
      $reply_id      = Wo_Secure($reply_id);
      $user_id       = Wo_Secure($user_id);
      $query_one     = "SELECT `id` FROM " . T_COMMENT_REPLIES_LIKES . " WHERE `reply_id` = {$reply_id} AND `user_id` = {$user_id}";
      $sql_query_one = mysqli_query($sqlConnect, $query_one);
      if (mysqli_num_rows($sql_query_one) >= 1) {
            return true;
      }
}
function Wo_CanSeeBirthday($user_id, $privacy) {
      global $sqlConnect, $wo;
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if ($privacy == 0) {
            return true;
      } elseif ($privacy == 1) {
            if (Wo_IsLogged() !== false) {
                  if (Wo_IsFollowing($wo['user']['user_id'], $user_id) === true) {
                        return true;
                  }
            } else {
                  return false;
            }
      } elseif ($privacy == 2) {
            return false;
      }
}
function Wo_CountPageInvites($page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      $page_id      = Wo_Secure($page_id);
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `active` = '1' AND `following_id` NOT IN (SELECT `invited_id` FROM " . T_PAGES_INVAITES . " WHERE `page_id` = {$page_id}) AND `following_id` NOT IN (SELECT `user_id` FROM " . T_PAGES_LIKES . " WHERE `page_id` = {$page_id})");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_GetPageInvites($page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      $data      = array();
      $page_id   = Wo_Secure($page_id);
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $query_one = mysqli_query($sqlConnect, "SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `active` = '1' AND `following_id` NOT IN (SELECT `invited_id` FROM " . T_PAGES_INVAITES . " WHERE `page_id` = {$page_id}) AND `following_id` NOT IN (SELECT `user_id` FROM " . T_PAGES_LIKES . " WHERE `page_id` = {$page_id})");
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $data[] = Wo_UserData($fetched_data['following_id']);
      }
      return $data;
}
function Wo_RegsiterInvite($user_id, $page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      if (Wo_IsPageInvited($user_id, $page_id) > 0) {
            return false;
      }
      if (Wo_PageExistsByID($page_id) === false) {
            return false;
      }
      $page_id        = Wo_Secure($page_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $query_one      = mysqli_query($sqlConnect, "INSERT INTO " . T_PAGES_INVAITES . " (`invited_id`,`inviter_id`,`page_id`) VALUES ({$user_id}, {$logged_user_id}, {$page_id})");
      if ($query_one) {
            $page                    = Wo_PageData($page_id);
            $notification_data_array = array(
                  'recipient_id' => $user_id,
                  'type' => 'invited_page',
                  'url' => 'index.php?tab1=timeline&u=' . $page['page_name']
            );
            Wo_RegisterNotification($notification_data_array);
            return true;
      }
}
function Wo_IsPageInvited($user_id, $page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      $page_id        = Wo_Secure($page_id);
      $user_id        = Wo_Secure($page_id);
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $query_one      = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_PAGES_INVAITES . " WHERE `invited_id` = {$user_id} AND `page_id` = {$page_id}");
      $fetched_data   = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_GetPageInviters($user_id, $page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      $data      = array();
      $page_id   = Wo_Secure($page_id);
      $query_one = mysqli_query($sqlConnect, "SELECT `inviter_id` FROM " . T_PAGES_INVAITES . " WHERE `invited_id` = {$user_id} AND `page_id` = {$page_id}");
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $data[] = Wo_UserData($fetched_data['inviter_id']);
      }
      return $data;
}
function Wo_DeleteInvites($user_id, $page_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if (empty($page_id) || !is_numeric($page_id)) {
            return false;
      }
      $page_id   = Wo_Secure($page_id);
      $user_id   = Wo_Secure($user_id);
      $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_PAGES_INVAITES . " WHERE `invited_id` = {$user_id} AND `page_id` = {$page_id}");
      if ($query_one) {
            return true;
      }
}
function Wo_GetCallInAction($id, $url) {
      global $sqlConnect, $wo;
      if (empty($id)) {
            return false;
      }
      if (!array_key_exists($id, $wo['call_action'])) {
            return false;
      }
      if (empty($url)) {
            return false;
      }
      $wo['call_page']['call_action_url'] = $url;
      $wo['call_page']['call_action_btn'] = $wo['call_action'][$id];
      return Wo_LoadPage('buttons/call-action');
}
function Wo_CountUserData($type) {
      global $wo, $sqlConnect;
      $type_table = T_USERS;
      $type_id    = 'user_id';
      $where      = '';
      if ($type == 'male') {
            $where = "`gender` = 'male'";
      } else if ($type == 'female') {
            $where = "`gender` = 'female'";
      } else if ($type == 'active') {
            $where = "`active` = '1'";
      } else if ($type == 'not_active') {
            $where = "`active` <> '1'";
      }
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table} WHERE {$where}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountPageData($type) {
      global $wo, $sqlConnect;
      $type_table = T_PAGES;
      $type_id    = 'id';
      $where      = '';
      if ($type == 'likes') {
            $type_table = T_PAGES_LIKES;
            $where      = "`active` = '1'";
            $type_id    = 'id';
      } else if ($type == 'pages_posts') {
            $type_table = T_POSTS;
            $where      = "`page_id` <> 0";
            $type_id    = 'id';
      } else if ($type == 'verified_pages') {
            $type_table = T_PAGES;
            $where      = "`verified` = '1'";
            $type_id    = 'page_id';
      }
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table} WHERE {$where}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountGroupData($type) {
      global $wo, $sqlConnect;
      $type_table = T_PAGES;
      $type_id    = 'id';
      $where      = '';
      if ($type == 'members') {
            $type_table = T_GROUP_MEMBERS;
            $where      = "`active` = '1'";
            $type_id    = 'id';
      } else if ($type == 'groups_posts') {
            $type_table = T_POSTS;
            $where      = "`group_id` <> 0";
            $type_id    = 'id';
      } else if ($type == 'join_requests') {
            $type_table = T_GROUP_MEMBERS;
            $where      = "`active` = '0'";
            $type_id    = 'id';
      }
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table} WHERE {$where}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountPostData($type) {
      global $wo, $sqlConnect;
      $type_table = T_PAGES;
      $type_id    = 'id';
      $where      = '';
      if ($type == 'replies') {
            $type_table = T_COMMENTS_REPLIES;
            $type_id    = 'id';
      } else if ($type == 'likes') {
            $type_table = T_LIKES;
            $type_id    = 'id';
      } else if ($type == 'wonders') {
            $type_table = T_WONDERS;
            $type_id    = 'id';
      }
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table}");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_CountGroupsNotMember($group_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id)) {
            return false;
      }
      $user_id      = Wo_Secure($wo['user']['user_id']);
      $group_id     = Wo_Secure($group_id);
      $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `active` = '1' AND `following_id` NOT IN (SELECT `user_id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id})");
      $fetched_data = mysqli_fetch_assoc($query_one);
      return $fetched_data['count'];
}
function Wo_GetGroupsNotMember($group_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id)) {
            return false;
      }
      $data      = array();
      $group_id  = Wo_Secure($group_id);
      $user_id   = Wo_Secure($wo['user']['user_id']);
      $query_one = mysqli_query($sqlConnect, "SELECT `following_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `active` = '1' AND `following_id` NOT IN (SELECT `user_id` FROM " . T_GROUP_MEMBERS . " WHERE `group_id` = {$group_id})");
      while ($fetched_data = mysqli_fetch_assoc($query_one)) {
            $data[] = Wo_UserData($fetched_data['following_id']);
      }
      return $data;
}
function Wo_GroupExistsByID($id) {
      global $sqlConnect;
      if (empty($id)) {
            return false;
      }
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_GROUPS . " WHERE `id`= '{$id}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_UserExistsById($id) {
      global $sqlConnect;
      if (empty($id)) {
            return false;
      }
      $id    = Wo_Secure($id);
      $query = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `user_id`= '{$id}' AND `active` = '1'");
      return (Wo_Sql_Result($query, 0) == 1) ? true : false;
}
function Wo_RegsiterGroupAdd($user_id, $group_id) {
      global $sqlConnect, $wo;
      if (Wo_IsLogged() === false) {
            return false;
      }
      if (empty($user_id) || !is_numeric($user_id)) {
            return false;
      }
      if (empty($group_id) || !is_numeric($group_id)) {
            return false;
      }
      if (Wo_IsGroupJoined($group_id, $user_id) === true) {
            return false;
      }
      if (Wo_GroupExistsByID($group_id) === false) {
            return false;
      }
      if (Wo_UserExistsById($user_id) === false) {
            return false;
      }
      if (Wo_IsGroupOnwer($group_id, $user_id)) {
            return false;
      }
      $logged_user_id = Wo_Secure($wo['user']['user_id']);
      $group_data     = Wo_GroupData($group_id);
      $user_id        = Wo_Secure($user_id);
      $query_one      = mysqli_query($sqlConnect, " INSERT INTO " . T_GROUP_MEMBERS . " (`user_id`,`group_id`,`active`,`time`) VALUES ({$user_id},{$group_id},'1'," . time() . ")");
      if ($query_one) {
            $notification_data_array = array(
                  'recipient_id' => $user_id,
                  'type' => 'added_you_to_group',
                  'url' => 'index.php?tab1=timeline&u=' . $group_data['group_name']
            );
            Wo_RegisterNotification($notification_data_array);
            return true;
      }
}
?>