<?php

use diversen\lang;

// Dummy translations
lang::translate('blocks');
lang::translate('blocks_unused');
lang::translate('blocks_sec');

$_INSTALL['VERSION'] = 5.01;
$_INSTALL['MAIN_MENU_ITEM'] = array (
    'title' => lang::translate('Blocks', null, array('no_translate' => true)),
    'url' => '/blocks/sort/index',
    'module_name' => 'blocks',
    'weight' => 100,
    'auth' => 'admin',
    'parent' => 0,
    'admin_only' => 1,
    'section' => 'settings'
);

$_INSTALL['PUBLIC_CLONE_URL'] = 'git://github.com/diversen/blocks.git';
$_INSTALL['PRIVATE_CLONE_URL'] = 'git@github.com:diversen/blocks.git';



