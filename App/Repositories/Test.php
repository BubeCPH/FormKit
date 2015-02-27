<?php
namespace App\Repositories;

//require_once REPO_PATH . 'BaseRepository.php';
require_once (UTIL_FUNC_PATH . 'getAllFunctions.php');

class Test extends BaseRepository {

    public $json;
    private $test;

    // the constructor!
    public function __construct($format = 'object') {
        parent::__construct($format);
        $this->PDOformat = $format;
    }

    public function getTest() {
        $da = new \KalnaBase\System\DatabaseAbstraction();
        $stmt = $da->select('tst', 'col')
                ->select('tst', 'id', 'tst_id')
                ->select('tst', 'id')
                ->from('test', 'tst')
//                ->startsWith('tst', 'col', 'T')
                ->endsWith('tst', 'col', 't')
                ->query($this->PDOformat);
        $activities = $stmt;

        return $this->format == 'json' ? json_encode($activities) : $activities;
    }

    public function getActivities($date = NULL, $onlyFavorites = TRUE) {
        if (empty($date)) {
            $date = date('d-m-Y');
        }
        $sql = "SELECT act.id
                     , act.parent_id
                     , act.code
                     , act.description
                     , act.favorites_yn
                     , act.sort_order
                     , IFNULL((SELECT 1 FROM registrations reg 
                               WHERE reg.user_id = act.user_id
                               AND   reg.activity_id = act.id
                               AND   reg.start_date = :p_date
                               AND   reg.end_time IS NULL), 0) active
                FROM activities act
                WHERE user_id = :p_user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $_SESSION['userId'], ':p_date' => $date));
        $this->activities = $stmt->fetchAll($this->PDOformat);

        if ($onlyFavorites == TRUE) {
            $activities = arrayTrim($this->activities, 'favorites_yn', 1, 'filter');
        } else {
            $activities = $this->activities;
        }
        return $this->format == 'json' ? json_encode($activities) : $activities;
    }

    public function getRegistrations($date) {
        $sql = "SELECT reg.id
                     , reg.activity_id
                     , reg.state
                     , act.code
                     , TIME_TO_SEC(reg.start_time) start_time
                     , TIME_TO_SEC(reg.end_time) end_time
                     , IFNULL(reg.description,act.description) description
                FROM  activities act
                    , registrations reg
                WHERE act.id = reg.activity_id
                AND   reg.start_date = :p_date
                AND   reg.user_id = :p_user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $_SESSION['userId'], ':p_date' => $date));
        $this->registrations = $stmt->fetchAll($this->PDOformat);
        if (empty($this->activities)) {
            $this->getActivities($date, FALSE);
        }
        $registrations = [];
        foreach ($this->registrations as $i => $element) {
//            $element['activity'] = arrayFetch($this->activities, 'id', $element[activity_id]);
//            $registrations[]=$element;

            if (is_array($element)) {
                $element['activity'] = arrayFetch($this->activities, 'id', $element[activity_id]);
            } elseif (is_object($element)) {
                $element->activity = arrayFetch($this->activities, 'id', $element->activity_id);
            }
            $registrations[] = $element;
        }
        return $this->format == 'json' ? json_encode($registrations) : $registrations;
    }

    public function getSummeries($date) {
        $sql = "SELECT code                       
                     , description            
                     , summery_seconds
                FROM  vregistration_summeries
                WHERE summery_type = 'activity'
                AND   start_date = :p_date
                AND   user_id = :p_user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $_SESSION['userId'], ':p_date' => $date));
        $this->summeries = $stmt->fetchAll($this->PDOformat);
        return $this->format == 'json' ? json_encode($this->summeries) : $this->summeries;
    }

    /**
     * Description of getDayTotal
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
            $this->totalSeconds += is_object($registration) ? $registration->summery_seconds : $registration['summery_seconds'];
        }
        return $this->totalSeconds;
    }

    /**
     * Properties
     */
    public $date;
    public $registration;
    public $registrations;
    public $registrationStates = array('STARTED', 'ENDED', 'DELETED', 'LOCKED', 'UNLOCKED');
    public $summeries; // = array();
    public $totalSeconds;
    public $activities;

    public function delete($userId, $registrationId) {
        $this->connection->beginTransaction();
        $stmt = $this->connection->prepare("DELETE FROM registrations WHERE id = :p_registration_id AND user_id = :p_user_id AND 1=1");
//        $stmt->bindValue(':p_user_id', $userId);
//        $stmt->bindValue(':p_registration_id', $registrationId);
        $stmt->execute(array(':p_user_id' => $this->registry->userId, ':p_registration_id' => $registrationId));
//        $result = $stmt->rowCount() == 1 ? TRUE : FALSE;
        $result = $stmt->rowCount() == 0 ? TRUE : FALSE;
        switch ($stmt->rowCount()) {
            case 0:
                $result = 'FALSE';
                $this->connection->rollBack();
                break;
            case 1:
                $result = 'TRUE';
                $this->connection->commit();
                break;
            default:
                $result = 'TO_MANY_RECORDS';
                $this->connection->rollBack();
                break;
        }
        $return = array('success' => $result);
        return $this->format == 'json' ? json_encode($return) : $return;
    }

    public function start($userId, $activityId, $date, $time, $returnNewRecord = FALSE) {
        $userId = $_SESSION['userId'];
        $stop = $this->connection->prepare("CALL kalnadk_udv_time.stop_all_registrations(:p_user_id, :p_date, :p_time)");
        $stop->execute(array(':p_user_id' => $userId, ':p_date' => $date, ':p_time' => $time));
        $start = $this->connection->prepare("CALL kalnadk_udv_time.create_registration(:p_user_id, :p_activity_id, :p_date, :p_time)");
        $start->execute(array(':p_user_id' => $userId, ':p_activity_id' => $activityId, ':p_date' => $date, ':p_time' => $time));
        if ($returnNewRecord) {
            $sql = "SELECT reg.id
                        , reg.activity_id
                        , reg.state
                        , TIME_TO_SEC(reg.start_time) start_time
                        , TIME_TO_SEC(reg.end_time) end_time
                        , reg.description
                   FROM  registrations  reg
                   WHERE reg.id = (SELECT max(r.id)
                                   FROM registrations r
                                   WHERE r.user_id = :p_user_id)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':p_user_id' => $userId));
            $this->registration = $stmt->fetchAll($this->PDOformat);
        } else {
            $this->registration = array('success' => 'TRUE');
        }
        return $this->format == 'json' ? json_encode($this->registration) : $this->registration;
    }

    public function stop($userId, $registrationId, $date, $time) {
        $userId = $_SESSION['userId'];
        $stop = $this->connection->prepare("CALL kalnadk_udv_time.stop_registration(:p_user_id, :p_reg_id, :p_date, :p_time)");
        $stop->execute(array(':p_user_id' => $userId, ':p_reg_id' => $registrationId, ':p_date' => $date, ':p_time' => $time));
        $this->registration = array('success' => 'TRUE');
        return $this->format == 'json' ? json_encode($this->registration) : $this->registration;
    }

    /**
     * 
     * @param array $param
     */
    public function update($userId, $registrationId, $startTime, $endTime, $description, $returnNewRecord = true) {
        $userId = $_SESSION['userId'];
        $stmt = $this->connection->prepare("CALL kalnadk_udv_time.update_registration(:p_user_id, :p_reg_id, :p_start_time, :p_end_time, :p_description)");
        $stmt->execute(array(':p_user_id' => $userId, ':p_reg_id' => $registrationId, ':p_start_time' => $startTime, ':p_end_time' => $endTime, ':p_description' => "$description"));
        if ($returnNewRecord) {
            $registrationIds = $stmt->fetchAll($this->PDOformat);
            $stmt->closeCursor();
            foreach ($registrationIds as $registrationId) {
                $registrations[] = $this->get($userId, $registrationId['id'], 'array');
            }
//        $this->registration
        } else {
            $registrations = array('success' => 'TRUE');
        }
        return $this->format == 'json' ? json_encode($registrations) : $registrations;
//        return "CALL kalnadk_udv_time.update_registration($userId, $registrationId, $startTime, $endTime, $description)";
    }

    public function get($userId, $registrationId, $format = NULL) {
        if ($format == NULL) {
            $format = $this->format;
        }
        $userId = $_SESSION['userId'];
        $sql = "SELECT reg.id
                     , reg.activity_id
                     , reg.state
                     , act.code
                     , TIME_TO_SEC(reg.start_time) start_time
                     , TIME_TO_SEC(reg.end_time) end_time
                     , IFNULL(reg.description,act.description) description
                FROM  activities act
                    , registrations reg
                WHERE act.id = reg.activity_id
                AND   reg.id = :p_reg_id
                AND   reg.user_id = :p_user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $userId, ':p_reg_id' => $registrationId));
        $this->registrations = $stmt->fetchAll($this->PDOformat);
        if (empty($this->activities)) {
            $this->getActivities(NULL, FALSE);
        }
        $registrations = [];
        foreach ($this->registrations as $i => $element) {
            if (is_array($element)) {
                $element['activity'] = arrayFetch($this->activities, 'id', $element[activity_id]);
            } elseif (is_object($element)) {
                $element->activity = arrayFetch($this->activities, 'id', $element->activity_id);
            }
            $registrations[] = $element;
        }
        return $format == 'json' ? json_encode($registrations) : $registrations;
    }

}
