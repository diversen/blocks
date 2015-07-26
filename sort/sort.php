<?php

if (!session::checkAccessControl('blocks_allow')){
    return;
}

moduleloader::includeModule('configdb');
moduleloader::includeModule ('blocks');

$blocks = blocks::getManipBlocks();
$data = array ();

try {
    foreach ($blocks as $key) {
        $data = array();
        
        if (!isset($_POST[$key])) {
            $data = array ();
        } else {      
            foreach ($_POST[$key] as $in_val) {
                $data[] = str_replace('-', '/', $in_val);
            }
        }
        configdb::set($key, $data, 'main');
    }
} catch (PDOException $e) {            
    q::rollBack();
    log::error($e->getTraceAsString());
    //return false;
}
q::commit();
die;
