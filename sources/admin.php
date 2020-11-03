<?php
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'admin';
$wo['title']       = $wo['lang']['admin_area'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('admin/content');