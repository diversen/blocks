<?php

if (!session::checkAccessControl('blocks_allow')){
    return; 
}

moduleloader::includeModule ('blocks');
$block = new blocks();

if (isset($_POST['submit'])) {
    if (empty(blocks::$errors)) {
        $id = $block->getId();
        $res = $block->delete($id);
        if ($res) {
            http::locationHeader('/blocks/custom/index', 
                    lang::translate('blocks_confirm_deleted'));
        } else {
            log::error('Should not happen');
        }        
    } else {
        html::errors(blocks::$errors);
    }
}

$block->form('delete');
