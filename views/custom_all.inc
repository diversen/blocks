<?php

foreach ($vars as $val ) {
    
    echo $val['title'] . "<br />\n";
    echo html::createLink("/blocks/custom/edit/$val[id]", lang::system('system_submit_edit'));
    echo MENU_SUB_SEPARATOR;
    echo html::createLink("/blocks/custom/delete/$val[id]", lang::system('system_submit_delete'));
    echo "<br />";
    
}