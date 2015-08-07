<?php

use diversen\lang;

$_INSTALL['VERSION'] = 4.01;
$_INSTALL['MAIN_MENU_ITEM'] = array (
    'title' => lang::translate('Blocks', null, array('no_translate' => true)),
    'url' => '/blocks/sort/index',
    'module_name' => 'blocks',
    'parent' => 0,
    'weight' => 100,
    'auth' => 'admin',
    'admin_only' => 1,
    'section' => 'settings'
);

$_INSTALL['PUBLIC_CLONE_URL'] = 'git://github.com/diversen/blocks.git';
$_INSTALL['PRIVATE_CLONE_URL'] = 'git@github.com:diversen/blocks.git';



