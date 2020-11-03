<?php
if (Wo_IsLogged() === false) {
  header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
  exit();
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'my_pages';
$wo['title']       = 'My Pages | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('page/my-pages');