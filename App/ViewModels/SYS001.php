<?php

namespace App\ViewModels;

use App\Repositories;

/**
 * Description of SYS001
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    26-04-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       SYS001
 * @version:    0.1
 * @desc:       class for 
 * 
 * @param
 * - foo are required
 * - bar are optional
 * 
 * @example
 * $m = new email ( "hello there",                           // foo
 *                  "how are you?"                           // bar
 *                );
 * 
 * $m->method();
 */
class SYS001 extends BaseViewModel {

    private $datatypes;

    // the constructor!
    public function __construct($task = null) {
        parent::__construct();
        $this->datatypes = new Repositories\DataTypes();
        if (!empty($_POST)) {
            $this->saveDataTypes();
        }
    }

    public function getDataTypes() {
        return $this->datatypes->getDataTypes();
    }

    private function saveDataTypes() {
        //TAB001
        $id = $_POST['NUM001'];
        $name = $_POST['STR001'];
        $datatype = $_POST['STR002'];
        $description = $_POST['STR003'];
//        print_r($name);

        $TAB001 = [];

        for ($i = 0; $i < count($id); $i++) {
            $TAB001[$i]['id'] = $id[$i];
            $TAB001[$i]['name'] = $name[$i];
            $TAB001[$i]['datatype'] = $datatype[$i];
            $TAB001[$i]['description'] = $description[$i];            
        }

        return $this->datatypes->saveDataTypes($TAB001);
    }

}
