<?php

include_once UTILPATH . 'ModelLoader.php';

class Settings {

    private $query;
    private $list;
    public $json;

    // the constructor!
    public function __construct() {
        $this->query = new SettingQuery();
    }

    public function findAll() {
        $this->list =  $this->query->create()->find();
    }

    public function findById($id) {
        $this->list =  $this->query->create()->findById($id);
    }

    public function get() {
        return $this->list;
    }

    public function getJSON() {
        return $this->list->toJSON(FALSE, TRUE);
    }
}
