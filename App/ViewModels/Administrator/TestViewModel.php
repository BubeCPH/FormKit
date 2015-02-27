<?php

namespace App\ViewModels\Administrator;

use App\ViewModels;
use App\Repositories;

//require_once REPO_PATH . 'Registrations.php';
//include_once UTILPATH . 'ModelLoader.php';
//include_once SYSPATH . 'TypeHint.php';
//require_once VIEWMODEL_PATH . 'BaseViewModel.php';

/**
 * Description of RegistreringViewModel
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    21-11-2013
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       RegistreringViewModel
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
//Typehint::initializeHandler();

class TestViewModel extends ViewModels\BaseViewModel {

    private $reg;

//    private $actQ;
    // the constructor!
    public function __construct($task = null) {
        parent::__construct();
        $this->reg = new Repositories\Test('json');
        $this->task = $task;
    }

    public function getTest() {
        $return = $this->reg->getTest();
        return $return;
    }

}
