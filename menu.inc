<?php

use diversen\lang;

$_MODULE_MENU[] = array(
        'title' => lang::translate('Sort blocks'),
        'url' => '/blocks/sort/index',
        'auth' => 'admin',
);


$_MODULE_MENU[] = array (
        'title' => lang::translate('View blocks'),
        'url' => '/blocks/custom/index',
        'auth' => 'admin',
);

$_MODULE_MENU[] = array(
        'title' => lang::translate('Add block'),
        'url' => '/blocks/custom/add',
        'auth' => 'admin',
);
