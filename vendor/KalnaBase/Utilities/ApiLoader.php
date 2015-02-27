<?php

namespace KalnaBase\Utilities;

/**
 * Description of ViewLoader
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    17-10-2013
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       ViewLoader
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
class ApiLoader {

    protected $apiPath;
    protected $apiAssetsPath = APIASSETSPATH;
    protected $apiSection = 'time';
    protected $apiVersion = CURRAPIVERSION;
    public $version;
    public $api;
    public $action;
    public $task;
    public $query;
    private $debug;
    private $debugI;
    private $file;
    protected $method;
    protected $vars = array();
    private $fileExtensions = array('.phtml', '.php');
    private $allowableMethods = array('POST', 'GET', 'PUT', 'DELETE');
    private $standardHeadfile = 'Head';
    private $standardAction = 'index';
    private $standardExtension = '.phtml';

    public function __construct($url) {
        //$this->api = BASEAPI;
        $this->apiPath = APIPATH . DIRSEP . $this->apiSection . DIRSEP . $this->apiVersion . DIRSEP . $this->api;
        $this->action = $this->standardAction;
        $this->debug = 1;
        $getMethod = strtoupper($_GET['method']);
        if (in_array($getMethod, $this->allowableMethods)) {
            $this->method = $getMethod;
        } else {
            $this->method = $_SERVER['REQUEST_METHOD'];
        }
        $this->url = $url;

        if (!isset($url)) {
            $this->debug = 2;
        } else {
            $this->debug = 3;
            //$url = routeURL($url);
            $urlArray = explode("/", $url);
            $this->apiPath = APIPATH;
            $this->debugI = 0;

            $this->apiSection = $urlArray[0];
            $this->apiPath = $this->apiPath . DIRSEP . $this->apiSection;
            array_shift($urlArray);

            if (preg_match('/^v\d$/', $urlArray[0])) {
                $this->debug = 9;
                $this->apiVersion = $urlArray[0];
                $this->apiPath = $this->apiPath . DIRSEP . $this->apiVersion;
                array_shift($urlArray);
            } else {
                $this->apiVersion = CURRAPIVERSION;
                $this->apiPath = $this->apiPath . DIRSEP . $this->apiVersion;
            }
            while (file_exists($this->apiPath . DIRSEP . $urlArray[0] . DIRSEP . $this->standardAction . $this->standardExtension)) {
                $this->debug = 4;
                $this->debugI++;
                $this->api = $urlArray[0];
                $this->file = $this->apiPath . DIRSEP . $this->api . DIRSEP . $this->standardAction . $this->standardExtension;
                $this->apiPath = $this->apiPath . DIRSEP . $this->api;

                array_shift($urlArray);
            }

            if (isset($urlArray[0]) && file_exists($this->apiPath . DIRSEP . $urlArray[0] . $this->standardExtension)) {
                $this->debug = 5;
                $this->action = $urlArray[0];
                array_shift($urlArray);
            } else {
                //$this->apiPath = $this->apiPath . DIRSEP . $this->api;
                $this->action = $this->standardAction;
            }
            if (isset($urlArray[0])) {
                $this->task = $urlArray[0];
                array_shift($urlArray);
            } else {
                $this->task = 'none'; // Default Task
            }
            $this->query = $urlArray;
        }

        if ($includeAssets) {
            $this->apiAssetsPath = $this->apiPath . '/Assets/';
        }
    }

    public function renderHtmlHead($headFile = NULL) {
        /**
         * TODO: Return error message
         * move HTTP-header logic into seperate class, to be called directly from here
         */
        if ($headFile == NULL) {
            $headFile = $this->standardHeadfile . $this->standardExtension;
            $this->apiAssetsPath = $this->apiAssetsPath;
        }
        if (file_exists($this->apiAssetsPath . $headFile)) {
            include $this->apiAssetsPath . $headFile;
        } else {
            throw new Exception('no template file ' . $headFile . ' present in directory ' . $this->apiAssetsPath);
        }
    }

    public function render() {
        if (isset($this->action) && $actionFile == NULL) {
            $actionFile = $this->action . $this->standardExtension;
        } elseif (!isset($this->action) && $actionFile == NULL) {
            $actionFile = $this->standardAction . $this->standardExtension;
        }

        $currViewParts = explode('.', $actionFile);
        $currViewExtension = '.' . end($currViewParts);

        if (!in_array($currViewExtension, $this->fileExtensions)) {
            $actionFile = $actionFile . $this->standardExtension;
        }

        if (file_exists($this->apiPath . DIRSEP . $actionFile)) {
            include $this->apiPath . DIRSEP . $actionFile;
        } else {
            throw new Exception('No actionfile present in directory ' . $this->apiPath . DIRSEP . '-|-' . 'apiVersion: ' . $this->apiVersion . '-|-' . 'Api: ' . $this->api . '-|-' . 'Action: ' . $this->action . ' (' . $actionFile . ')' . '-|-' . 'Task: ' . $this->task . '-|-' . 'Debug: ' . $this->debug);
        }
    }

    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function __get($name) {
        return $this->vars[$name];
    }

}
