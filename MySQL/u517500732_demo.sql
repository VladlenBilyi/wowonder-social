
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Anamakine: localhost
-- Üretim Zamanı: 02 Şub 2016, 03:53:39
-- Sunucu sürümü: 10.0.20-MariaDB
-- PHP Sürümü: 5.2.17

--
-- scriptarsivim@windowslive.com
--
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Veritabanı: `u517500732_demo`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Activities`
--

CREATE TABLE IF NOT EXISTS `Wo_Activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `post_id` int(255) NOT NULL,
  `activity_type` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `activity_type` (`activity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Ads`
--

CREATE TABLE IF NOT EXISTS `Wo_Ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) CHARACTER SET latin1 NOT NULL,
  `code` text CHARACTER SET latin1 NOT NULL,
  `active` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Tablo döküm verisi `Wo_Ads`
--

INSERT INTO `Wo_Ads` (`id`, `type`, `code`, `active`) VALUES
(1, 'header', 'http://www.enkral.net', '0'),
(2, 'sidebar', 'http://www.enkral.net', '0'),
(4, 'footer', 'http://www.enkral.net', '0');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Albums_Media`
--

CREATE TABLE IF NOT EXISTS `Wo_Albums_Media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Announcement`
--

CREATE TABLE IF NOT EXISTS `Wo_Announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `time` int(32) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Announcement_Views`
--

CREATE TABLE IF NOT EXISTS `Wo_Announcement_Views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `announcement_id` (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Apps`
--

CREATE TABLE IF NOT EXISTS `Wo_Apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_user_id` int(11) NOT NULL,
  `app_name` varchar(32) NOT NULL,
  `app_website_url` varchar(55) NOT NULL,
  `app_description` text NOT NULL,
  `app_avatar` varchar(100) NOT NULL DEFAULT 'upload/photos/app-default-icon.png',
  `app_callback_url` varchar(255) NOT NULL,
  `app_id` varchar(32) NOT NULL,
  `app_secret` varchar(55) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Apps_Permission`
--

CREATE TABLE IF NOT EXISTS `Wo_Apps_Permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Banned_Ip`
--

CREATE TABLE IF NOT EXISTS `Wo_Banned_Ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Blocks`
--

CREATE TABLE IF NOT EXISTS `Wo_Blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_from` int(11) NOT NULL,
  `block_to` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_CommentLikes`
--

CREATE TABLE IF NOT EXISTS `Wo_CommentLikes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `comment_id` (`comment_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Comments`
--

CREATE TABLE IF NOT EXISTS `Wo_Comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `c_file` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_CommentWonders`
--

CREATE TABLE IF NOT EXISTS `Wo_CommentWonders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Comment_Replies`
--

CREATE TABLE IF NOT EXISTS `Wo_Comment_Replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Comment_Replies_Likes`
--

CREATE TABLE IF NOT EXISTS `Wo_Comment_Replies_Likes` (
  `id` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Comment_Replies_Wonders`
--

CREATE TABLE IF NOT EXISTS `Wo_Comment_Replies_Wonders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reply_id` (`reply_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Config`
--

CREATE TABLE IF NOT EXISTS `Wo_Config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Tablo döküm verisi `Wo_Config`
--

INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES
(1, 'siteName', 'WoWonder'),
(2, 'siteTitle', 'WoWonder Social Network Platform'),
(3, 'siteKeywords', 'social, wowonder, social site'),
(4, 'siteDesc', 'WoWonder v1.1 is a Social Networking Platform. With our new feature, user can wonder posts, photos,'),
(5, 'siteEmail', 'info@example.com'),
(6, 'defualtLang', 'english'),
(7, 'emailValidation', '0'),
(8, 'emailNotification', '1'),
(9, 'fileSharing', '1'),
(10, 'seoLink', '1'),
(11, 'cacheSystem', '0'),
(12, 'chatSystem', '1'),
(13, 'useSeoFrindly', '1'),
(14, 'reCaptcha', '0'),
(15, 'reCaptchaKey', ''),
(16, 'user_lastseen', '1'),
(17, 'age', '1'),
(18, 'deleteAccount', '1'),
(19, 'connectivitySystem', '0'),
(20, 'profileVisit', '1'),
(21, 'maxUpload', '1000000000'),
(22, 'maxCharacters', '320'),
(23, 'message_seen', '1'),
(24, 'message_typing', '1'),
(25, 'google_map_api', 'AIzaSyBOfpaMO_tMMsuvS2T4zx4llbtsFqMuT9Y'),
(26, 'allowedExtenstion', 'jpg,png,jpeg,gif,docx,zip,rar,pdf,doc,mp3,mp4,wav,txt'),
(27, 'censored_words', ''),
(28, 'googleAnalytics', ''),
(29, 'AllLogin', '1'),
(30, 'googleLogin', '1'),
(31, 'facebookLogin', '1'),
(32, 'twitterLogin', '1'),
(33, 'linkedinLogin', '1'),
(34, 'VkontakteLogin', '1'),
(35, 'facebookAppId', ''),
(36, 'facebookAppKey', ''),
(37, 'googleAppId', ''),
(38, 'googleAppKey', ''),
(39, 'twitterAppId', ''),
(40, 'twitterAppKey', ''),
(41, 'linkedinAppId', ''),
(42, 'linkedinAppKey', ''),
(43, 'VkontakteAppId', ''),
(44, 'VkontakteAppKey', ''),
(45, 'theme', 'wowonder'),
(47, 'second_post_button', 'wonder'),
(48, 'instagramAppId', ''),
(49, 'instagramAppkey', ''),
(50, 'instagramLogin', '1'),
(51, 'header_background', '#444444'),
(52, 'header_hover_border', '#666666'),
(53, 'header_color', '#ffffff'),
(54, 'body_background', '#f9f9f9'),
(55, 'btn_color', '#ffffff'),
(56, 'btn_background_color', '#a84849'),
(57, 'btn_hover_color', '#ffffff'),
(58, 'btn_hover_background_color', '#c45a5b'),
(59, 'setting_header_color', '#ffffff'),
(60, 'setting_header_background', '#a84849'),
(61, 'setting_active_sidebar_color', '#ffffff'),
(62, 'setting_active_sidebar_background', '#a84849'),
(63, 'setting_sidebar_background', '#ffffff'),
(64, 'setting_sidebar_color', '#444444'),
(65, 'logo_extension', 'png'),
(66, 'online_sidebar', '1');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Followers`
--

CREATE TABLE IF NOT EXISTS `Wo_Followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `following_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `is_typing` int(11) NOT NULL DEFAULT '0',
  `active` int(255) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `following_id` (`following_id`),
  KEY `follower_id` (`follower_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Games`
--

CREATE TABLE IF NOT EXISTS `Wo_Games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_name` varchar(50) NOT NULL,
  `game_avatar` varchar(100) NOT NULL,
  `game_link` varchar(100) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Games_Players`
--

CREATE TABLE IF NOT EXISTS `Wo_Games_Players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `last_play` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`game_id`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Groups`
--

CREATE TABLE IF NOT EXISTS `Wo_Groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_name` varchar(32) NOT NULL,
  `group_title` varchar(32) NOT NULL,
  `avatar` varchar(120) NOT NULL DEFAULT 'upload/photos/d-group.jpg ',
  `cover` varchar(120) NOT NULL DEFAULT 'upload/photos/d-cover.jpg  ',
  `about` varchar(550) NOT NULL,
  `category` int(11) NOT NULL DEFAULT '1',
  `privacy` enum('1','2') NOT NULL DEFAULT '1',
  `join_privacy` enum('1','2') NOT NULL DEFAULT '1',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `registered` varchar(32) NOT NULL DEFAULT '0/0000',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `privacy` (`privacy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Group_Members`
--

CREATE TABLE IF NOT EXISTS `Wo_Group_Members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Hashtags`
--

CREATE TABLE IF NOT EXISTS `Wo_Hashtags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) CHARACTER SET latin1 NOT NULL,
  `tag` varchar(255) NOT NULL,
  `last_trend_time` int(255) NOT NULL,
  `trend_use_num` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_trend_time` (`last_trend_time`),
  KEY `trend_use_num` (`trend_use_num`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Tablo döküm verisi `Wo_Hashtags`
--

INSERT INTO `Wo_Hashtags` (`id`, `hash`, `tag`, `last_trend_time`, `trend_use_num`) VALUES
(1, 'f5ed632801676aafd18e959c9d9ac02c', 'ucuz', 1454385121, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Likes`
--

CREATE TABLE IF NOT EXISTS `Wo_Likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Messages`
--

CREATE TABLE IF NOT EXISTS `Wo_Messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `text` text CHARACTER SET latin1 NOT NULL,
  `media` varchar(255) CHARACTER SET latin1 NOT NULL,
  `mediaFileName` varchar(200) NOT NULL,
  `mediaFileNames` varchar(200) NOT NULL,
  `time` int(255) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`),
  KEY `to_id` (`to_id`),
  KEY `seen` (`seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Notifications`
--

CREATE TABLE IF NOT EXISTS `Wo_Notifications` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `notifier_id` int(255) NOT NULL,
  `recipient_id` int(255) NOT NULL,
  `post_id` int(255) NOT NULL,
  `page_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `seen_pop` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `type2` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `seen` int(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notifier_id` (`notifier_id`),
  KEY `user_id` (`recipient_id`),
  KEY `post_id` (`post_id`),
  KEY `seen` (`seen`),
  KEY `time` (`time`),
  KEY `page_id` (`page_id`),
  KEY `group_id` (`group_id`,`seen_pop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Pages`
--

CREATE TABLE IF NOT EXISTS `Wo_Pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_name` varchar(32) NOT NULL,
  `page_title` varchar(32) NOT NULL,
  `page_description` varchar(1000) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'upload/photos/d-page.jpg',
  `cover` varchar(255) NOT NULL DEFAULT 'upload/photos/d-cover.jpg',
  `page_category` int(11) NOT NULL DEFAULT '1',
  `website` varchar(255) NOT NULL,
  `facebook` varchar(32) NOT NULL,
  `google` varchar(32) NOT NULL,
  `vk` varchar(32) NOT NULL,
  `twitter` varchar(32) NOT NULL,
  `linkedin` varchar(32) NOT NULL,
  `company` varchar(32) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `address` varchar(100) NOT NULL,
  `call_action_type` int(11) NOT NULL,
  `call_action_type_url` varchar(255) NOT NULL,
  `instgram` varchar(32) NOT NULL,
  `youtube` varchar(100) NOT NULL,
  `verified` enum('0','1') NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `registered` varchar(32) NOT NULL DEFAULT '0/0000',
  PRIMARY KEY (`page_id`),
  KEY `page_id` (`page_id`,`page_category`),
  KEY `registered` (`registered`),
  KEY `user_id` (`user_id`,`page_name`,`verified`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Pages_Invites`
--

CREATE TABLE IF NOT EXISTS `Wo_Pages_Invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `inviter_id` int(11) NOT NULL,
  `invited_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`,`inviter_id`,`invited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Pages_Likes`
--

CREATE TABLE IF NOT EXISTS `Wo_Pages_Likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`user_id`,`page_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_PinnedPosts`
--

CREATE TABLE IF NOT EXISTS `Wo_PinnedPosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `page_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `post_id` int(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `active` (`active`),
  KEY `page_id` (`page_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Posts`
--

CREATE TABLE IF NOT EXISTS `Wo_Posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `postText` text NOT NULL,
  `page_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `postLink` varchar(100) NOT NULL,
  `postLinkTitle` varchar(100) NOT NULL,
  `postLinkImage` varchar(100) NOT NULL,
  `postLinkContent` varchar(100) NOT NULL,
  `postVimeo` varchar(100) NOT NULL,
  `postDailymotion` varchar(100) NOT NULL,
  `postFacebook` varchar(100) NOT NULL,
  `postFile` varchar(255) CHARACTER SET latin1 NOT NULL,
  `postFileName` varchar(200) NOT NULL,
  `postYoutube` varchar(255) CHARACTER SET latin1 NOT NULL,
  `postVine` varchar(32) NOT NULL,
  `postSoundCloud` varchar(255) CHARACTER SET latin1 NOT NULL,
  `postMap` varchar(255) NOT NULL,
  `postShare` int(11) NOT NULL,
  `postPrivacy` enum('0','1','2','3') NOT NULL DEFAULT '1',
  `postType` varchar(30) NOT NULL,
  `postFeeling` varchar(255) NOT NULL,
  `postListening` varchar(255) NOT NULL,
  `postTraveling` varchar(255) NOT NULL,
  `postWatching` varchar(255) NOT NULL,
  `postPlaying` varchar(255) NOT NULL,
  `time` int(255) NOT NULL,
  `registered` varchar(32) NOT NULL DEFAULT '0/0000',
  `album_name` varchar(52) NOT NULL,
  `multi_image` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `postFile` (`postFile`),
  KEY `postShare` (`postShare`),
  KEY `postType` (`postType`),
  KEY `postYoutube` (`postYoutube`),
  KEY `page_id` (`page_id`),
  KEY `group_id` (`group_id`),
  KEY `registered` (`registered`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Tablo döküm verisi `Wo_Posts`
--

INSERT INTO `Wo_Posts` (`id`, `post_id`, `user_id`, `recipient_id`, `postText`, `page_id`, `group_id`, `postLink`, `postLinkTitle`, `postLinkImage`, `postLinkContent`, `postVimeo`, `postDailymotion`, `postFacebook`, `postFile`, `postFileName`, `postYoutube`, `postVine`, `postSoundCloud`, `postMap`, `postShare`, `postPrivacy`, `postType`, `postFeeling`, `postListening`, `postTraveling`, `postWatching`, `postPlaying`, `time`, `registered`, `album_name`, `multi_image`) VALUES
(1, 1, 1, 0, '[a]http%3A%2F%2Fwww.enkral.net[/a]', 0, 0, 'http://www.enkral.net', 'Enkral Script ve Tema İndir – Ücretsiz Script &amp; Tema Arşivi', 'upload/photos/2016/02/na8eB848wvgLSwHclLcM_url_image.png', 'Yepyeni Script ve Temalarla Geri Döndük! Enkral, Scripti indir, Ücretsiz Script İndir, Ücretsiz Tema', '', '', '', '', '', '', '', '', '', 0, '0', 'post', '', '', '', '', '', 1454384927, '2/2016', '', '0'),
(2, 2, 1, 0, 'Biri #[1] alan adımı dedi? Promosyonları görüntüle; [a]http%3A%2F%2Fenkral.org%2Fdomain-registration%2Fpromos.php[/a]', 0, 0, 'http://enkral.org/domain-registration/promos.php', 'Şu an ki promosyonlar', '', '2,81 TL&#039;ye alan adı olur mu demeyin biz yaptık oldu! :)', '', '', '', '', '', '', '', '', '', 0, '0', 'post', 'happy', '', '', '', '', 1454385121, '2/2016', '', '0');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_RecentSearches`
--

CREATE TABLE IF NOT EXISTS `Wo_RecentSearches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `search_id` int(11) NOT NULL,
  `search_type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`search_id`),
  KEY `search_type` (`search_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Reports`
--

CREATE TABLE IF NOT EXISTS `Wo_Reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `seen` (`seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_SavedPosts`
--

CREATE TABLE IF NOT EXISTS `Wo_SavedPosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Tokens`
--

CREATE TABLE IF NOT EXISTS `Wo_Tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `token` varchar(200) NOT NULL,
  `time` int(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`),
  KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Users`
--

CREATE TABLE IF NOT EXISTS `Wo_Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `avatar` varchar(100) NOT NULL DEFAULT 'upload/photos/d-avatar.jpg',
  `cover` varchar(100) NOT NULL DEFAULT 'upload/photos/d-cover.jpg',
  `background_image` varchar(100) NOT NULL,
  `background_image_status` enum('0','1') NOT NULL,
  `relationship_id` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `working` varchar(32) NOT NULL,
  `working_link` varchar(32) NOT NULL,
  `about` text NOT NULL,
  `school` varchar(32) NOT NULL,
  `gender` varchar(32) NOT NULL DEFAULT 'male',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `country_id` int(11) NOT NULL DEFAULT '1',
  `website` varchar(50) NOT NULL,
  `facebook` varchar(50) NOT NULL,
  `google` varchar(50) NOT NULL,
  `twitter` varchar(50) NOT NULL,
  `linkedin` varchar(32) NOT NULL,
  `vk` varchar(32) NOT NULL,
  `instagram` varchar(32) NOT NULL,
  `language` varchar(31) NOT NULL,
  `email_code` varchar(32) NOT NULL,
  `src` varchar(32) NOT NULL DEFAULT 'Undefined',
  `ip_address` varchar(32) NOT NULL,
  `follow_privacy` enum('1','0') NOT NULL DEFAULT '0',
  `post_privacy` varchar(255) NOT NULL DEFAULT 'ifollow',
  `message_privacy` enum('1','0') NOT NULL DEFAULT '0',
  `confirm_followers` enum('1','0') NOT NULL DEFAULT '0',
  `show_activities_privacy` enum('0','1') NOT NULL DEFAULT '1',
  `birth_privacy` enum('0','1','2') NOT NULL DEFAULT '0',
  `visit_privacy` enum('0','1') NOT NULL DEFAULT '0',
  `verified` enum('1','0') NOT NULL DEFAULT '0',
  `lastseen` int(32) NOT NULL DEFAULT '0',
  `showlastseen` enum('1','0') NOT NULL DEFAULT '1',
  `emailNotification` enum('1','0') NOT NULL DEFAULT '1',
  `e_liked` enum('0','1') NOT NULL DEFAULT '1',
  `e_wondered` enum('0','1') NOT NULL DEFAULT '1',
  `e_shared` enum('0','1') NOT NULL DEFAULT '1',
  `e_followed` enum('0','1') NOT NULL DEFAULT '1',
  `e_commented` enum('0','1') NOT NULL DEFAULT '1',
  `e_visited` enum('0','1') NOT NULL DEFAULT '1',
  `e_liked_page` enum('0','1') NOT NULL DEFAULT '1',
  `e_mentioned` enum('0','1') NOT NULL DEFAULT '1',
  `e_joined_group` enum('0','1') NOT NULL DEFAULT '1',
  `e_accepted` enum('0','1') NOT NULL DEFAULT '1',
  `e_profile_wall_post` enum('0','1') NOT NULL DEFAULT '1',
  `status` enum('1','0') NOT NULL DEFAULT '0',
  `active` enum('0','1','2') NOT NULL DEFAULT '0',
  `admin` enum('0','1') NOT NULL DEFAULT '0',
  `type` varchar(11) NOT NULL DEFAULT 'user',
  `registered` varchar(32) NOT NULL DEFAULT '0/0000',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `active` (`active`),
  KEY `admin` (`admin`),
  KEY `src` (`src`),
  KEY `gender` (`gender`),
  KEY `avatar` (`avatar`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `registered` (`registered`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Tablo döküm verisi `Wo_Users`
--

INSERT INTO `Wo_Users` (`user_id`, `username`, `email`, `password`, `first_name`, `last_name`, `avatar`, `cover`, `background_image`, `background_image_status`, `relationship_id`, `address`, `working`, `working_link`, `about`, `school`, `gender`, `birthday`, `country_id`, `website`, `facebook`, `google`, `twitter`, `linkedin`, `vk`, `instagram`, `language`, `email_code`, `src`, `ip_address`, `follow_privacy`, `post_privacy`, `message_privacy`, `confirm_followers`, `show_activities_privacy`, `birth_privacy`, `visit_privacy`, `verified`, `lastseen`, `showlastseen`, `emailNotification`, `e_liked`, `e_wondered`, `e_shared`, `e_followed`, `e_commented`, `e_visited`, `e_liked_page`, `e_mentioned`, `e_joined_group`, `e_accepted`, `e_profile_wall_post`, `status`, `active`, `admin`, `type`, `registered`) VALUES
(1, 'admin', 'info@enkral.net', '25d55ad283aa400af464c76d713c07ad', '', '', 'upload/photos/d-avatar.jpg', 'upload/photos/d-cover.jpg', '', '0', 0, '', '', '', '', '', 'male', '0000-00-00', 1, '', '', '', '', '', '', '', 'turkish', '', 'Site', '', '0', 'ifollow', '0', '0', '1', '0', '0', '1', 1454385200, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', 'user', '0/0000');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Verification_Requests`
--

CREATE TABLE IF NOT EXISTS `Wo_Verification_Requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `seen` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Wo_Wonders`
--

CREATE TABLE IF NOT EXISTS `Wo_Wonders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
