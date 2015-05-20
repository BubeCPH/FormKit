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
    public $getRequest;
    public $request = array();
    public $jsonRequest;
    public $data = "";
    public $errors = [];
    private $dedicatedApiFunctions = ['metadata','generateModel'];

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
//        var_dump($this->PDOformat);
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
        } elseif ($this->getRequestMethod() === "GET") {
            $this->getTableData();
        } elseif ($this->getRequestMethod() === "POST") {
            $this->postTableData();
        } elseif ($this->getRequestMethod() === "PUT") {
            $this->putTableData();
        } elseif ($this->getRequestMethod() === "DELETE") {
            $this->deleteTableData();
        }
    }

    public function getReferer() {
        return $_SERVER['HTTP_REFERER'];
    }

    public function response($status, $data = NULL) {
        $this->code = ($status) ? $status : 200;
        $this->setHeaders();
        echo ($data) ? $data : $this->getStatusMessage;
        exit;
    }

    // For a list of http codes checkout http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    protected function getStatusMessage() {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            209 => 'Updated',   //Not standard
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            409 => 'Conflict',
            480 => 'No such collection',   //Not standard
            481 => 'No such instance collection',   //Not standard
            500 => 'Internal Server Error',
            501 => 'Not Implemented');
        return ($status[$this->code]) ? $status[$this->code] : $status[500];
    }
    protected function getErrorTitle($code) {
        $errorTitle = array(
            900 => 'General error',
            901 => 'Value requered',
            902 => 'Value too long',
            920 => 'No such collection',
            921 => 'No such instance collection',
            930 => 'A delete request shall provide an entity identicator',
            960 => 'General DBA error');
        return ($errorTitle[$code]) ? $errorTitle[$code] : NULL;
    }
    protected function setError($code, $detail) {
        $this->errors[] = ['code' => $code, 'title' => $this->getErrorTitle($code), 'detail' => $detail];
    }

    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function inputs() {
//        $method = strtoupper(filter_input(INPUT_GET, 'method'));
//        print_r($method);
        switch ($this->getRequestMethod()) {
            case "POST":
                $this->getRequest = $this->cleanInputs($_GET);
                $this->request = $this->cleanInputs($_POST);
                $this->jsonRequest = json_decode(file_get_contents("php://input"));
                break;
            case "GET":
            case "DELETE":
                $this->getRequest = $this->cleanInputs($_GET);
                $this->request = $this->cleanInputs($_GET);
                break;
            case "PUT":
                $this->getRequest = $this->cleanInputs($_GET);
                $file_get_contents = file_get_contents("php://input");
                parse_str($file_get_contents, $this->request);
                $this->request = $this->cleanInputs($this->request);
                $this->jsonRequest = json_decode($file_get_contents);
                break;
            default:
                $this->response('', 406);
                break;
        }
//        var_dump($this->jsonRequest);
//        echo "\n";
//        echo '$this->request' . "\n";
//        print_r($this->request);
        $this->request['request'] = explode("/", $this->getRequest['request']);
//        var_dump($this->request);
        if (!trim($this->request['request'][0]) == "") {
            $this->request['function'] = trim(Functions\stringFunctions::transform(Functions\stringFunctions::transform($this->request['request'][0], Functions\stringFunctions::UNDER_SCORE), Functions\stringFunctions::CAMEL_CASE));
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
