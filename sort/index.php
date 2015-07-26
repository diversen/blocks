<?php

if (!session::checkAccessControl('blocks_allow')){
    return;
}
layout::$blocksContent;
moduleloader::includeModule ('blocks');
$blocks_js = config::getModulePath('blocks') . "/assets/sort.js";
template::setInlineCss(config::getModulePath('blocks') . "/assets/sort.css");;

$search = array ();
$search[] = '{blocks_js_ids}';
$search[] = '{blocks_js_data}';

$replace = array ();
$replace[] = blocks::getJsIds();
$replace[] = blocks::getJsData();

            //$replace = $code;
            template::setInlineJs(
                $blocks_js, 
                // load last or close to. 
                10000, 
                array ('no_cache'   => 1, 
                       'search'     => $search, 
                       'replace'    => $replace)
            );


blocks::getBlocksFull();
return;
