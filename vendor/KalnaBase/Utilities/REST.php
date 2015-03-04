<?php

namespace KalnaBase\Utilities;

use KalnaBase\System;
use KalnaBase\Utilities;
use KalnaBase\Utilities\Functions;

/**
 * Description of REST
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    31-08-2014
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       REST
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
class REST {

    private $method = "";
    private $code = 200;
    protected $PDOformat;
    protected $format;
    protected $registry;
    protected $appConfig;
    public $connection;
    public $allow = array();
    public $content_type = "application/json";
    public $request = array();
    public $data = "";
    private $dedicatedApiFunctions = ['metadata'];

    public function __construct($format = 'object') {
        $this->inputs();
        $this->registry = Utilities\SessionRegistry::getInstance();
        $this->appConfig = System\AppConfig::getInstance();
        $this->format = $format;
        switch ($this->format) {
            case 'object':
                $this->PDOformat = \PDO::FETCH_OBJ;
                break;
            case 'array':
                $this->PDOformat = \PDO::FETCH_ASSOC;
                break;
            case 'json':
                $this->PDOformat = \PDO::FETCH_ASSOC;
                break;
            default:
                $this->PDOformat = \PDO::FETCH_OBJ;
                break;
        }
        $this->connection = new System\DatabaseAbstraction($this->PDOformat);
    }

    /*
     * Dynmically call the method based on the query string
     */

    public function processApi() {
//        $func = strtolower($this->request['collection']);
        $func = $this->request['function'];
//        print_r($func);
//        print_r($this->request);
        if (in_array($func, $this->dedicatedApiFunctions) && (int) method_exists($this, $func) > 0) {
            $this->$func();
        } else {
            $this->tableData();
//            $this->response($func, 404); // If the method not exist with in this class "Page not found".
        }
    }

    public function getReferer() {
        return $_SERVER['HTTP_REFERER'];
    }

    public function response($data, $status) {
        $this->code = ($status) ? $status : 200;
        $this->setHeaders();
        echo $data;
        exit;
    }

    // For a list of http codes checkout http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    protected function getStatusMessage() {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            404 => 'Not Found',
            406 => 'Not Acceptable');
        return ($status[$this->code]) ? $status[$this->code] : $status[500];
    }

    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function inputs() {
//        $method = strtoupper(filter_input(INPUT_GET, 'method'));
//        print_r($method);
        switch ($this->getRequestMethod()) {
            case "POST":
                $this->request = $this->cleanInputs($_POST);
                break;
            case "GET":
            case "DELETE":
                $this->request = $this->cleanInputs($_GET);
                break;
            case "PUT":
                parse_str(file_get_contents("php://input"), $this->request);
                $this->request = $this->cleanInputs($this->request);
                break;
            default:
                $this->response('', 406);
                break;
        }
//        echo "\n";
//        echo '$this->request' . "\n";
//        print_r($this->request);
        $this->request['request'] = explode("/", $this->request['request']);
        if (!trim($this->request['request'][0]) == "") {
            $this->request['function'] = trim(Functions\stringFunctions::transform($this->request['request'][0], Functions\stringFunctions::CAMEL_CASE));
            if (in_array($this->request['function'], $this->dedicatedApiFunctions)) {
                array_shift($this->request['request']);
            }
            $this->request['collection'] = Functions\stringFunctions::transform($this->request['request'][0], Functions\stringFunctions::UNDER_SCORE);
        }
        if (!$this->request['request'][1] == "") {
            $this->request['instance'] = Functions\stringFunctions::transform($this->request['request'][1], Functions\stringFunctions::UNDER_SCORE);
        } else {
            $this->request['instance'] = null;
        }
        if (!$this->request['request'][2] == "") {
            $this->request['instanceCollection'] = Functions\stringFunctions::transform($this->request['request'][2], Functions\stringFunctions::UNDER_SCORE);
        } else {
            $this->request['instanceCollection'] = null;
        }
        if (!trim($this->request['expand']) == "") {
            $this->request['expand'] = Functions\stringFunctions::transform($this->request['expand'], Functions\stringFunctions::UNDER_SCORE);
        }

//        echo "\n";
//        echo "\n";
//        print_r($this->request);
//        die('__'.$this->getRequestMethod());
    }

    private function cleanInputs($data) {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (substr($k, 0, strlen('$')) == '$') {
                    $k = substr($k, strlen('$'));
                }
                $clean_input[$k] = $this->cleanInputs($v);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            if (substr($data, 0, strlen('$')) == '$') {
                $data = substr($data, strlen('$'));
            }
            $clean_input = trim($data);
        }
//        print_r($clean_input);
        return $clean_input;
    }

    /*
     * 	Encode array into JSON
     */

    protected function json($data) {
        if (is_array($data)) {
            return json_encode($data);
        }
    }

    protected function setHeaders() {
        header("HTTP/1.1 " . $this->code . " " . $this->getStatusMessage());
        header("Content-Type:" . $this->content_type);
    }

}
