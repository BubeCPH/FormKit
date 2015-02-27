<?php

require_once REPO_PATH . 'Registrations.php';
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

class RegistrationsViewModel extends BaseViewModel {

    private $reg;

//    private $actQ;
    // the constructor!
    public function __construct($task = null) {
        parent::__construct();
        $this->reg = new Registrations('json');
        $this->task = $task;
        try {
            $date = date_parse($task);
            if ($date != FALSE) {
                $dateString = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
//                $this->registry->CurrDate = $dateString;
                $this->registry->date = $dateString;
                $this->date = $dateString;
            }
        } catch (Exception $exc) {
//            $this->registry->CurrDate = $this->registry->date;
        }

//            $this->debug = $this->registry->get('CurrDate');
//        if ($this->registry->exists('CurrDate')) {
//            $this->debug = $this->registry->sql_date;
//            $this->date = $this->registry->CurrDate;
//        } else {
//            $this->debug = 0;
//            $this->registry->CurrDate = $this->registry->date;
//            $this->date = $this->registry->CurrDate;
//        }
        $this->debug = $this->registry->date;
    }

    public function getActivities() {
        $return = $this->reg->getActivities($this->registry->date, FALSE);
        $this->activities = $this->reg->activities;
        return $return;
    }

    public function getRegistrations() {
        return $this->reg->getRegistrations($this->registry->date);
    }

    public function getSummeries($date) {
        $return = $this->reg->getSummeries($date);
        $this->summeries = $this->reg->summeries;
        return $return;
    }

    /**
     * Description of time_to_decimal
     * 
     * Convert time into decimal time.
     *
     * @param string $time The relevant date in 'yyyy-mm-dd'-format
     *
     * @return integer The time in seconds.
     */
    public function getDayTotal($date) {
        if (empty($this->summeries)) {
            $this->getSummeries($date);
        }
        $this->totalSeconds = 0;
        foreach ($this->summeries as $registration) {
            $this->totalSeconds += $registration->summery_seconds;
        }
        return $this->totalSeconds;
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
