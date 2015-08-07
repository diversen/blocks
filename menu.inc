<?php

use diversen\lang;

$_MODULE_MENU[] = array(
        'title' => lang::translate('Sort blocks', null ,array ('no_translate' => true)),
        'url' => '/blocks/sort/index',
        'auth' => 'admin',
);


$_MODULE_MENU[] = array (
        'title' => lang::translate('View blocks', null ,array ('no_translate' => true)),
        'url' => '/blocks/custom/index',
        'auth' => 'admin',
);

$_MODULE_MENU[] = array(
        'title' => lang::translate('Add block', null ,array ('no_translate' => true)),
        'url' => '/blocks/custom/add',
        'auth' => 'admin',
);
