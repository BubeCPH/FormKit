<?php

require_once REPO_PATH . 'BaseRepository.php';
require_once UTIL_FUNC_PATH . 'getAllFunctions.php';
require_once REPO_PATH . 'Activities.php';

class Summeries extends BaseRepository {

    public $json;
    public $summeries;
    public $act;
    public $activities;
    public $periodTypes = array('period', 'weekly', 'monthly');

    // the constructor!
    public function __construct($format = 'object') {
        parent::__construct($format);
    }

    public function fetchSummeries($start_date, $end_date = NULL) {
        $sql = "SELECT * "
                . "FROM vregistration_summeries "
                . "WHERE user_id = :userid "
                . "AND date BETWEEN :start_date AND IFNULL(:end_date, NOW())";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':userid' => $_SESSION['userId'], ':start_date' => $start_date, ':end_date' => $end_date));
        $this->summeries = $stmt->fetchAll($this->PDOformat);
        $stmt->closeCursor();
    }

    public function getSummeries($start_date, $end_date = NULL) {
        empty($this->summeries) ? self::fetchSummeries($start_date, $end_date) : NULL;

        return $this->format == 'json' ? json_encode($this->summeries) : $this->summeries;
    }

    public function getDates($start_date, $end_date = NULL) {
        $startDate = new DateTime($start_date);
        $endDate = empty($end_date) ? new DateTime() : new DateTime($end_date);
        $dateDiff = $startDate->diff($endDate);

        $dateArray = [];
        $dateArray[] = $startDate->format('Y-m-d');
        for ($i = 0; $i < $dateDiff->days; $i++) {
            $startDate->add(new DateInterval('P1D'));
            $dateArray[] = $startDate->format('Y-m-d');
        }
        return $dateArray; //$this->format == 'json' ? json_encode($dateArray) : $dateArray;
    }

    public function getWeeksInYear($year = NULL) {
        $year = empty($year) ? $this->registry->year : $year;
        $week = 1;
        $date = new DateTime();
        $weekArray = [];
        while ($date->setISODate($year, $week)->format('o') == $year) {
            $weekArray[] = $week;
            $week++;
        }
        return $this->format == 'json' ? json_encode($weekArray) : $weekArray;
    }

    public function currDate() {
        if (empty($this->registry->date)) {
            $this->registry->date = date('Y-m-d');
        }
        return $this->registry->date;
    }

    public function currYearWeek() {
        $ddate = $this->currDate();
        $duedt = explode("-", $ddate);
        $date = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $week = (int) date('W', $date);
        $year = (int) date('o', $date);
        return array('year' => $year, 'week' => $week);
    }

    public function currYearMonth() {
        $ddate = $this->currDate();
        $duedt = explode("-", $ddate);
        $date = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $month = (int) date('m', $date);
        $year = (int) date('o', $date);
        return array('year' => $year, 'month' => $month);
    }

    public function buildCalendar() {
        $num_args = func_num_args() - 1;
        if (!in_array(func_get_arg(0), $this->periodTypes)) {
            return null;
        }

        if (func_get_arg(0) == $this->periodTypes[0] && ($num_args == 1 || $num_args == 2)) {
            if ($num_args == 1) {
                $start_date = func_get_arg(1);
            } else {
                $start_date = func_get_arg(1);
                $end_date = func_get_arg(2);
            }
        } elseif (func_get_arg(0) == $this->periodTypes[1] && type_is_int(func_get_arg(1)) && type_is_int(func_get_arg(2))) {
            $year = intval(func_get_arg(1));
            $week = intval(func_get_arg(2));

            $date = new DateTime();

            $date->setISODate($year, $week);
            $start_date = $date->format('Y-m-d');

            $end_date = $date->setISODate($year, $week, 7)->format('Y-m-d');
            //echo $date->format('Y-m-d');
        } elseif (func_get_arg(0) == $this->periodTypes[2] && type_is_int(func_get_arg(1)) && is_int(func_get_arg(2))) {
            $year = func_get_arg(1);
            $month = func_get_arg(2);

            $date = new DateTime();

            $date->setDate($year, $month, 1);
            $start_date = $date->format('Y-m-d');

            $end_date = $date->format('Y-m-t');

            //echo $date->format('Y-m-d');
        } else {
            $arguments = "";
            for ($i = 0; $i < func_num_args(); $i++) {
                $arguments .= "Argument " . $i . ": " . func_get_arg($i) . "(" . gettype(func_get_arg($i)) . ")" . "\n";
            }
            return array('error' => 'exception', 'message' => 'Wrong number or types of arguments' . "\n" . $arguments);
        }

        $this->act = new Activities('array');
        empty($this->summeries) ? self::fetchSummeries($start_date, $end_date) : NULL;
        $activities = $this->act->getActivities();

        $calArray = [];
        $dateArray = [];
        $dates = $this->getDates($start_date, $end_date);

        $dateStarttimeArray = [];
        $dateEndtimeArray = [];
        foreach ($dates as $dateValue) {
            $dateArray[] = array('type' => 'date', 'value' => $dateValue);
            $startValues = arrayFetch(arrayTrim($this->summeries, 'date', $dateValue), 'type', 'starttime');
            $endValues = arrayFetch(arrayTrim($this->summeries, 'date', $dateValue), 'type', 'endtime');
            $sumValues = arrayFetch(arrayTrim($this->summeries, 'date', $dateValue), 'type', 'day');
            if (count($startValues)) {
                $dateStarttime = $startValues['seconds'];
            } else {
                $dateStarttime = null;
            }
            $dateStarttimeArray[] = array('type' => 'startingTime', 'value' => $dateStarttime);

            if (count($endValues)) {
                $dateEndtime = $endValues['seconds'];
            } else {
                $dateEndtime = null;
            }
            $dateEndtimeArray[] = array('type' => 'endingTime', 'value' => $dateEndtime);

            if (count($sumValues)) {
                $dateSumtime = $sumValues['seconds'];
            } else {
                $dateSumtime = null;
            }
            $dateSumtimeArray[] = array('type' => 'sumTime', 'value' => $dateSumtime);
        }
        $activitiesArray = [];
        $i = 0;
        foreach ($activities as $actValue) {
            $activitiesValues = arrayTrim(arrayTrim($this->summeries, 'activity_id', $actValue['id']), 'type', 'activity');
            $activityName = $actValue['description'];
//            $activitiesValues[] = arrayTrim2($this->summeries, 'activity_id', $actValue['id']);
            $registrationArray = [];
            foreach ($dates as $dateValue) {
                if (count($activitiesValues)) {
                    $registrationValues = arrayTrim($activitiesValues, 'date', $dateValue);
                    if (count($registrationValues)) {
                        $registrationTime = $registrationValues[0]['seconds'];
                    } else {
                        $registrationTime = 0;
                    }
                } else {
                    $registrationTime = 0;
                }
                $registrationArray[] = array('type' => 'registrationTime', 'value' => $registrationTime, 'activity' => $activityName);
            }
            $activitiesArray[] = $registrationArray;
            $i++;
        }

        $calArray[] = $dateArray;
        $calArray[] = $dateStarttimeArray;
        $calArray[] = $dateEndtimeArray;
        $calArray[] = $dateSumtimeArray;
        $endArray = array_merge($calArray, $activitiesArray);
//        $calArray[] = $activities;



        return $this->format == 'json' ? json_encode($endArray) : $endArray;
    }

    public function getActivityTotal() {
        $sql = "SELECT activity_id
                     , code                       
                     , description            
                     , summery_seconds
                FROM  vregistration_summeries
                WHERE summery_type = 'activitytotal'
                AND   user_id = :userid";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':userid' => $this->registry->userId));
        $this->summeries = $stmt->fetchAll($this->PDOformat);
        return $this->format == 'json' ? json_encode($this->summeries) : $this->summeries;
    }

}
