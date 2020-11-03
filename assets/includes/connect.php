<?php
require 'config.php';
error_reporting(0);
// Connect to SQL Server
$sqlConnect   = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $ServerErrors[] = "Required PHP_VERSION >= 5.4.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n";
}

if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}

$baned_ips = Wo_GetBanned('user');

if (in_array($_SERVER["REMOTE_ADDR"], $baned_ips)) {
    exit();
}

$config = Wo_GetConfig();

$wo                  = array();
// Config Url
$config['theme_url'] = $site_url . '/themes/' . $config['theme'];
$config['site_url']  = $site_url;
$wo['config']        = $config;
$wo['emo']           = $emo;
$wo['site_pages']    = array('home', 'welcome' ,'activate', 'search', 'timeline', 'pages', 'page', 'groups', 'group', 'create-group', 'group-setting', 'create-page', 'setting', 'page-setting', 'messages', 'logout', '404', 'post', 'games', 'admincp', 'saved-posts', 'hashtag', 'terms','contact-us','albums', 'album', 'game');

$http_header = 'http://';
if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}

$wo['actual_link']   = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
// Define Cache Vireble
$cache               = new Cache();
if ($wo['config']['cacheSystem'] == 1) {
    $cache->Wo_OpenCacheDir();
}
// Login With Url
$wo['facebookLoginUrl']   = $config['site_url'] . '/login-with.php?provider=Facebook';
$wo['twitterLoginUrl']    = $config['site_url'] . '/login-with.php?provider=Twitter';
$wo['googleLoginUrl']     = $config['site_url'] . '/login-with.php?provider=Google';
$wo['linkedInLoginUrl']   = $config['site_url'] . '/login-with.php?provider=LinkedIn';
$wo['VkontakteLoginUrl']  = $config['site_url'] . '/login-with.php?provider=Vkontakte';
$wo['instagramLoginUrl']  = $config['site_url'] . '/login-with.php?provider=Instagram';
// Defualt User Pictures 
$wo['userDefaultAvatar'] = 'upload/photos/d-avatar.jpg';
$wo['userDefaultCover']  = 'upload/photos/d-cover.jpg';


$wo['pageDefaultAvatar'] = 'upload/photos/d-page.jpg';

$wo['groupDefaultAvatar'] = 'upload/photos/d-group.jpg';


// Get LoggedIn User Data
if (Wo_IsLogged() === true) {
    $wo['user_session'] = (isset($_SESSION['user_id'])) ? Wo_Secure($_SESSION['user_id']) : Wo_Secure($_COOKIE['user_id']);
    $wo['user']         = Wo_UserData($wo['user_session']);
    if (!empty($wo['user']['language'])) {
        if (file_exists('assets/languages/' . $wo['user']['language'] . '.php')) {
            $_SESSION['lang'] = $wo['user']['language'];
        }
    }
    if ($wo['user']['user_id'] < 0 || empty($wo['user']['user_id']) || !is_numeric($wo['user']['user_id']) || Wo_UserActive($wo['user']['username']) === false) {
        header("Location: " . Wo_SeoLink('index.php?tab1=logout'));
    }
}
// Language Function
if (isset($_GET['lang']) AND !empty($_GET['lang'])) {
    $lang_name = Wo_Secure(strtolower($_GET['lang']));
    $lang_path = 'assets/languages/' . $lang_name . '.php';
    if (file_exists($lang_path)) {
        $_SESSION['lang'] = $lang_name;
        if (Wo_IsLogged() === true) {
            mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `language` = '" . $lang_name . "' WHERE `user_id` = " . Wo_Secure($wo['user']['user_id']));
            if ($wo['config']['cacheSystem'] == 1) {
                $cache->delete(md5($wo['user']['user_id']) . '_U_Data.tmp');
            }
        }
    }
}
if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $wo['config']['defualtLang'];
}
$wo['language']      = $_SESSION['lang'];
$wo['language_type'] = 'ltr';
// Add rtl languages here.
$rtl_langs           = array(
    'arabic'
);
// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($wo['language'] == strtolower($lang)) {
        $wo['language_type'] = 'rtl';
    }
}
// Icons Virables
$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
// Include Language File
require('assets/languages/' . $wo['language'] . '.php');

$wo['second_post_button_icon'] = ($config['second_post_button'] == 'wonder') ? 'exclamation-circle': 'thumbs-down';
$wo['second_post_button_text'] = ($config['second_post_button'] == 'wonder') ? $wo['lang']['wonder']: $wo['lang']['dislike'];
$wo['second_post_button_texts'] = ($config['second_post_button'] == 'wonder') ? $wo['lang']['wonders']: $wo['lang']['dislikes'];

$wo['marker'] = '?';
if ($wo['config']['seoLink'] == 0) {
   $wo['marker'] = '&';
}

$wo['feelingIcons'] = array(
    'happy' => 'smile',
    'loved' => 'heart-eyes',
    'sad' => 'disappointed',
    'so_sad' => 'sob',
    'angry' => 'angry',
    'confused' => 'confused',
    'smirk'  => 'smirk',
    'broke' => 'broken-heart',
    'expressionless' => 'expressionless', 
    'cool' => 'sunglasses',
    'funny' => 'joy',
    'tired' => 'tired-face',
    'lovely' => 'heart',
    'blessed' => 'innocent',
    'shocked' => 'scream',
    'sleepy' => 'sleeping',
    'pretty' => 'relaxed',
    'bored' => 'unamused'
);

?>