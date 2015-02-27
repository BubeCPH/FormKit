<?php

require_once REPO_PATH . 'Summeries.php';
require_once REPO_PATH . 'Activities.php';
include_once UTILPATH . 'ModelLoader.php';
//include_once SYSPATH . 'TypeHint.php';
require_once VIEWMODEL_PATH . 'BaseViewModel.php';

/**
 * Description of OverviewViewModel;

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

class OverviewViewModel extends BaseViewModel {

    private $sum;

    public function __construct() {
        parent::__construct();
        $this->sum = new Summeries('json');
        $this->task = empty($this->task) ? 'period' : $this->task;
    }

    public function getCalendar($arg0, $arg1, $arg2 = NULL) {
        $this->summeries = $this->sum->buildCalendar($arg0, $arg1, $arg2);
        return $this->summeries;
    }

    public function getWeeksInYear($year = NULL) {
        return $this->sum->getWeeksInYear($year);
    }

    public function currDate() {
        if (empty($this->registry->date)) {
            $this->registry->date = date('Y-m-d');
        }
        return $this->registry->date;
    }

    public function currYearWeek() {
        return array('year' => $this->registry->year, 'week' => $this->registry->week);
    }

    public function currYearMonth() {
        return array('year' => $this->registry->year, 'month' => $this->registry->month);
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
