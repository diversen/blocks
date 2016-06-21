<?php

namespace modules\blocks;

use diversen\conf;
use diversen\db;
use diversen\db\q;
use diversen\html;
use diversen\lang;
use diversen\log;
use diversen\moduleloader;
use diversen\session;
use diversen\uri;


use modules\configdb\module as configdb;

class module {

    public function indexAction() {
        if (!session::checkAccessFromModuleIni('blocks_allow')) {
            return;
        }
    }


    /**
     *
     * @return array $blocks all blocks set in main config 
     */
    public static function getBlocks() {
        $blocks = conf::getMainIni('blocks_all');
        
        $blocks = explode(',', $blocks);
        return $blocks;
    }

    /**
     *
     * @return array $blocks all valid blocks that can be moved 
     */
    public static function getManipBlocks() {
        $blocks = conf::getModuleIni('blocks_blocks');
        $blocks = explode(',', $blocks);
        return $blocks;
    }

    /**
     * echo full html blocks  
     */
    public static function getBlocksFull() {
        $blocks = self::getBlocks();

        $valid_blocks = self::getManipBlocks();

        $str = self::getListStart();
        $unused = array();
        foreach ($blocks as $val) {
            if (in_array($val, $valid_blocks)) {
                $values = conf::getMainIni($val);
                if (empty($values)) {
                    $values = array();
                }

                // compute the difference between database entry and file entry
                // so we can keep on adding entries to file, which user
                // can add. 

                $from_file = conf::getMainIniFromFile($val);
                if (!$from_file) {
                    $from_file = array();
                }

                $diff = array_diff($from_file, $values);
                $unused = array_merge($unused, $diff);
                $str.= self::getOlBlock($values, $val);
            }
        }


        $values = conf::getMainIni('blocks_unused');
        if (!$values) {
            $values = array();
        }
        $values = array_merge($values, $unused);
        $values = array_unique($values);
        if (!$values){
            $values = array();
        }
        $str.= self::getOlBlock($values, 'blocks_unused');
        $str.= self::getListEnd();

        $success = lang::translate('Blocks has been sorted');
        $str.= "<div class = \"manip_success\">$success</div>\n";
        echo $str;
    }

    /**
     * 
     * @return string $str returns id to be used with javacript 
     */
    public static function getJsIds() {
        $blocks = self::getManipBlocks();
        $str = '';
        foreach ($blocks as $val) {
            $str.="#$val, ";
        }
        $str = rtrim($str, ', ');
        return $str;
    }

    /**
     * 
     * @return string 
     */
    public static function getJsData() {
        $blocks = self::getManipBlocks();
        $str = '';
        foreach ($blocks as $val) {
            $str.="$val:$(\"#$val\").sortable('toArray'), ";
        }
        $str = rtrim($str, ', ');
        return $str;
    }

    /**
     * @param array $values the values of the block 
     * @param string $name the name of the block
     * @return string $str the ol of the block 
     */
    public static function getOlBlock($values, $name) {
        static $num = 1;
        static $count = 0;

        $str = "<h3>" . lang::translate($name) . "</h3>\n";
        $str.= "<ol id=\"$name\" class =\"connectedSortable\">\n";
        $num++;

        if (empty($values)) {
            $values = array();
        }
        foreach ($values as $val) {

            // check for custom blocks
            if (is_numeric($val)) {
                //print_r($val);
                $val_str = $val;

                $row = self::getOne($val);
                $name = $row['title'];
            } else {
                $val_str = str_replace('/', '-', $val);
                $name = lang::translate($val . "-human");
            }

            $str.= "<li id=\"$val_str\">$name</li>";
            $count++;
        }
        $str.="</ol>\n";
        return $str;
    }

    /**
     * 
     * @return string $str the div start 
     */
    public static function getListStart() {
        $str = '';
        $str.= "<div id=\"sortable\">\n";
        return $str;
    }

    /**
     * 
     * @return string $str the div end 
     */
    public static function getListEnd() {
        $str = "</div>\n";
        return $str;
    }

    public static function form($action = 'add', $vars = array()) {
        self::sanitize();

        if ($action == 'delete') {
            html::formStart('content_article_form');
            html::legend(lang::translate('Delete block'));
            html::submit('submit', lang::translate('Delete'));
            html::formEnd();
            echo html::getStr();
            return;
        }

        html::$autoLoadTrigger = 'submit';
        if ($action == 'update') {
            $id = self::getId();



            $vars = self::getOne($id);
            $legend = lang::translate('Edit block');
        } else {

            $legend = lang::translate('Add block');
        }


        html::init($vars);
        html::formStart('blocks_add');
        html::legend($legend);
        html::label('title', lang::translate('Title'));
        html::text('title');

        $label = lang::translate('Content') . '<br />';
        $label.= moduleloader::getFiltersHelp(conf::getModuleIni('blocks_filters'));

        html::label('content_block', $label);
        html::textarea('content_block', null, array('class' => 'markdown'));

        html::label('show_title', lang::translate('Display title'));
        html::checkbox('show_title');

        html::submit('submit', lang::translate('Submit'));
        html::formEnd();

        echo html::getStr();
    }

    public static $errors = null;

    public static function sanitize() {
        if (isset($_POST['submit'])) {
            $_POST = html::specialEncode($_POST);
        }

        if (empty($_POST['title'])) {
            self::$errors['title'] = lang::translate('Insert a title');
        }

        if (empty($_POST['content_block'])) {
            self::$errors['content_block'] = lang::translate('No block content');
        }

        if (!isset($_POST['show_title'])) {
            $_POST['show_title'] = 0;
        }
    }

    /**
     * inserts a block into blocks table,
     * add the block to blocks_unused
     * @return boolean $res true on success and false on failure
     */
    public function insert() {

        db::$dbh->beginTransaction();
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);

        // we add to blocks_unused
        $res = $db->insert('blocks', $values);

        if (!$res) {
            // should not happen
            db::$dbh->rollBack();
            return;
        }

        $insert_id = db::$dbh->lastInsertId();
        $unused = conf::getMainIni('blocks_unused');

        if (!is_array($unused)){
            $unused = array();
        }
        array_push($unused, $insert_id);

        configdb::set('blocks_unused', $unused, 'main');
        return db::$dbh->commit();
    }

    /**
     * delete from blocks, 
     * traverse all blocks and remove id if set anywhere 
     * @param int $id the block id to delete
     * @return boolean $res true on success and false on failure
     */
    public function delete($id) {
        q::begin();

        try {
            $db = new db();
            $res = q::delete('blocks')->filter('id =', $id)->exec();

            if (!$res) {
                // should not happen
                q::rollback();
                return;
            }

            // traverse blocks and remove element if set 
            //$data = array();
            $blocks = self::getManipBlocks();
            foreach ($blocks as $val) {
                $data = conf::getMainIni($val);

                foreach ($data as $in_key => $in_val) {
                    if ($in_val == $id) {
                        unset($data[$in_key]);
                    }
                }
                //print_r($data); die;
                configdb::set($val, $data, 'main');
            }
        } catch (PDOException $e) {
            q::rollBack();
            log::error($e->getTraceAsString());
            return false;
        }
        return q::commit();
    }

    /**
     * updates a row in blocks table
     * @return boolean $res true on success and false on failure 
     */
    public function update() {

        $id = self::getId();

        db::begin();
        $db = new db();
        $values = db::prepareToPost();
        $values = html::specialDecode($values);
        $db->update('blocks', $values, $id);

        //$insert_id = db::$dbh->lastInsertId();
        $unused = conf::getMainIni('blocks_unused');

        if (!is_array($unused))
            $unused = array();
        if (!in_array($id, $unused)) {
            array_push($unused, $id);
            configdb::set('blocks_unused', $unused, 'main');
        }
        return db::commit();
    }

    /**
     * get all rows in block manip table
     * @return array $rows rows in table 
     */
    public static function getAllFromDb() {
        $db = new db();
        $rows = $db->selectAll('blocks');
        return $rows = html::specialEncode($rows);
    }

    /**
     * get one row from blocks
     * @param int $id
     * @return array $row 
     */
    public static function getOne($id) {
        $db = new db();
        return $row = $db->selectOne('blocks', 'id', $id);
    }

    /**
     * display all rows in blocks 
     */
    public static function displayAll() {
        $all = self::getAllFromDb();

        foreach ($all as $val) {

            echo $val['title'] . "<br />\n";
            echo html::createLink("/blocks/custom/edit/$val[id]", lang::translate('Edit'));
            echo MENU_SUB_SEPARATOR;
            echo html::createLink("/blocks/custom/delete/$val[id]", lang::translate('Delete'));
            echo "<br />";
        }
    }

    /**
     * gets id from url. Used on update and delete. 
     * @return mixed $id should return int
     */
    public static function getId() {
        return $id = uri::getInstance()->fragment(3);
    }

    /**
     * used with sub module
     * @param int $id
     * @return string $url 
     */
    public static function getReturnUrlFromId($id) {
        return "/blocks/custom/edit/$id";
    }

    /**
     * gets link from id
     * @param int $id
     * @return string $link 
     */
    public static function getLinkFromId($id) {
        $item = self::getOne($id);
        $url = self::getReturnUrlFromId($id);
        $title = html::specialEncode($item['title']);
        return html::createLink($url, $title);
    }

    /**
     * gets redirect 
     * @param int $id
     * @return string $url return url 
     */
    public static function getRedirect($id) {
        return self::getReturnUrlFromId($id);
    }
}
