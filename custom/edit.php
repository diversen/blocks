<?php

if (!session::checkAccessControl('blocks_allow')){
    return;
}

moduleloader::includeModule ('blocks');
$block = new blocks();

if (isset($_POST['submit'])) {
    $block->sanitize();
    if (empty(blocks::$errors)) {
        $res = $block->update();
        if ($res) {
            http::locationHeader('/blocks/custom/index', 
                    lang::translate('blocks_confirm_insert'));
        } else {
            log::error('Should not happen');
        }        
    } else {
        html::errors(blocks::$errors);
    }
}

$id = blocks::getId();
echo $block->includeSubModules($id);
$block->form('update');
