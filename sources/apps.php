<?php

/* API / Developers (will be available on future updates) */

die();

if (Wo_IsLogged() === false) {

  header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));

  exit();

}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'app_setting';
$wo['title']       = $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('graph/apps');