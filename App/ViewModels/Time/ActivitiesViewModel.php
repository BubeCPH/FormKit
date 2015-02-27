<?php

require_once REPO_PATH . 'Activities.php';
include_once UTILPATH . 'ModelLoader.php';
//include_once SYSPATH . 'TypeHint.php';
require_once VIEWMODEL_PATH . 'BaseViewModel.php';

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

class ActivitiesViewModel extends BaseViewModel {

    private $act;

//    private $actQ;
    // the constructor!
    public function __construct() {
        parent::__construct();
        $this->act = new Activities('json');
    }

    public function getActivities() {
        $return = $this->act->getActivities();
        $this->activities = $this->act->activities;
        return $return;
    }

    /**
     * Properties
     */
    public $date;
    public $registrations;
    public $registrationStates = array('STARTED', 'ENDED', 'DELETED', 'LOCKED', 'UNLOCKED');
    public $summeries; // = array();
    public $totalSeconds;
    public $activities;

}
