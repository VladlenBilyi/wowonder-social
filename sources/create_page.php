<?php
if (Wo_IsLogged() === false) {
  header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
  exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'create_page';
$wo['title']       = 'Create Page | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('page/create-page');