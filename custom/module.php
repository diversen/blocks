<?php

namespace modules\blocks\custom;

use diversen\html;
use diversen\http;
use diversen\lang;
use diversen\log;
use diversen\moduleloader;
use diversen\session;

use modules\blocks\module as blocks;

class module {

    public function addAction() {
        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('blocks');

        $block = new blocks();

        if (isset($_POST['submit'])) {
            $block->sanitize();
            if (empty(blocks::$errors)) {
                $res = $block->insert();
                if ($res) {
                    http::locationHeader('/blocks/custom/index', lang::translate('Block has been added'));
                } else {
                    log::error('Should not happen');
                }
            } else {
                html::errors(blocks::$errors);
            }
        }

        $block->form('add');
    }

    public function deleteAction() {
        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('blocks');
        $block = new blocks();

        if (isset($_POST['submit'])) {
            if (empty(blocks::$errors)) {
                $id = $block->getId();
                $res = $block->delete($id);
                if ($res) {
                    http::locationHeader('/blocks/custom/index', lang::translate('Block has been deleted'));
                } else {
                    log::error('Should not happen');
                }
            } else {
                html::errors(blocks::$errors);
            }
        }

        $block->form('delete');
    }

    public function editAction() {

        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('blocks');
        $block = new blocks();

        if (isset($_POST['submit'])) {
            $block->sanitize();
            if (empty(blocks::$errors)) {
                $res = $block->update();
                if ($res) {
                    http::locationHeader('/blocks/custom/index', lang::translate('Block has been inserted'));
                } else {
                    log::error('Should not happen');
                }
            } else {
                html::errors(blocks::$errors);
            }
        }
        $block->form('update');
    }

    public function indexAction() {

        if (!session::checkAccessControl('blocks_allow')) {
            return;
        }

        moduleloader::includeModule('blocks');
        blocks::displayAll();
    }
}
