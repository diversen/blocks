<?php

namespace modules\blocks\sort;

use diversen\conf;
use diversen\db\q;
use diversen\log;
use diversen\moduleloader;
use diversen\session;
use diversen\template\assets;

use modules\blocks\module as blocks;
use modules\configdb\module as configdb;

class module {

    public function indexAction() {

        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('blocks');
        $blocks_js = conf::getModulePath('blocks') . "/assets/sort.js";
        
        assets::setInlineCss(conf::getModulePath('blocks') . "/assets/sort.css");

        $search = array();
        $search[] = '{blocks_js_ids}';
        $search[] = '{blocks_js_data}';

        $replace = array();
        $replace[] = blocks::getJsIds();
        $replace[] = blocks::getJsData();

        assets::setInlineJs(
                $blocks_js,
                // load last or close to. 
                10000, array('no_cache' => 1,
            'search' => $search,
            'replace' => $replace)
        );


        blocks::getBlocksFull();
        return;
    }

    public function sortAction() {

        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('configdb');
        moduleloader::includeModule('blocks');

        $blocks = blocks::getManipBlocks();
        $data = array();
        
        // transaction
        q::begin();
        try {
            foreach ($blocks as $key) {
                $data = array();

                if (!isset($_POST[$key])) {
                    $data = array();
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
            return false;
        }
        q::commit();
        die;
    }

}
