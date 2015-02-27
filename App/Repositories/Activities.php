<?php

require_once REPO_PATH . 'BaseRepository.php';
require_once (UTIL_FUNC_PATH . 'getAllFunctions.php');

class Activities extends BaseRepository {
    public $json;

    // the constructor!
    public function __construct($format = 'object') {
        parent::__construct($format);
    }

    public function getActivity($key, $value) {
        $sql = "SELECT *
                FROM  activities
                WHERE user_id = :userid";
        //$bindArray = array(':userid' => $_SESSION['userId']);
//        if (!empty($key)) {
//            $sql = $sql . " AND " . $key . " = :" . $key;
//            $bindArray[':id'] = $value;
//        }
//        return $sql;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':userid' => $this->registry->userId));
        $this->activities = $stmt->fetchAll($this->PDOformat);
        $stmt->closeCursor();
//
//        if ($onlyFavorites == TRUE) {
//            $activities = arrayTrim($this->activities, 'favorites_yn', 1, 'filter');
//        } else {
//            $activities = $this->activities;
//        }
        return $this->format == 'json' ? json_encode($this->activities) : $this->activities;
    }

    public function getActivities($onlyFavorites = FALSE) {
        $sql = "SELECT * "
                . "FROM activities "
                . "WHERE user_id = :userid";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':userid' => $_SESSION['userId']));
        $this->activities = $stmt->fetchAll($this->PDOformat);
        $stmt->closeCursor();

        if ($onlyFavorites) {
            $activities = arrayTrim($this->activities, 'favorites_yn', 1, 'filter');
        } else {
            $activities = $this->activities;
        }
        return $this->format == 'json' ? json_encode($activities) : $activities;
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
        $stmt->closeCursor();
        return $this->format == 'json' ? json_encode($this->summeries) : $this->summeries;
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

    /**
     * 
     * @param array $param
     */
    public function add($parentId, $code, $description, $favorite, $sortOrder) {
        $sqlI = "INSERT INTO activities (parent_id, user_id, code, description, favorites_yn, sort_order) "
                . "VALUES (:p_parent_id, :p_user_id, :p_code, :p_description, :p_favorites, :p_sort_order) ";
        $stmt = $this->connection->prepare($sqlI);
        $stmt->execute(array(':p_parent_id' => $parentId,
                             ':p_user_id' => $this->registry->userId,
                             ':p_code' => $code,
                             ':p_description' => $description,
                             ':p_favorites' => $favorite,
                             ':p_sort_order' => $sortOrder));
        $stmt->closeCursor();
        $sqlS = "SELECT * "
                . "FROM activities "
                . "WHERE id = LAST_INSERT_ID()"
                . "AND user_id = :userid";
        $slct = $this->connection->prepare($sqlS);
        $slct->execute(array(':userid' => $this->registry->userId));
        $this->activities = $slct->fetchAll($this->PDOformat);
        $slct->closeCursor();
        return $this->format == 'json' ? json_encode($this->activities) : $this->activities;
    }

    public function delete($activityId) {
        $this->connection->beginTransaction();
        $sql = "DELETE FROM activities "
                . "WHERE id = :p_activity_id "
                . "AND user_id = :p_user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $this->registry->userId, ':p_activity_id' => $activityId));
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

    /**
     * 
     * @param array $param
     */
    public function update($activityId, $code, $description, $favorite, $sortOrder) {
        $sql = "UPDATE activities "
                . "SET code = :p_code AND description = :p_description AND favorites_yn = :p_favorites AND sort_order = :p_sort_order "
                . "WHERE id = :p_activity_id AND user_id = :p_user_id ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':p_user_id' => $this->registry->userId, 
                             ':p_activity_id' => $activityId, 
                             ':p_code' => $code, 
                             ':p_description' => $description, 
                             ':p_favorites' => $favorite, 
                             ':p_sort_order' => $sortOrder));
    }

}
