<?php

namespace KalnaBase\Utilities;

use KalnaBase\System;
use KalnaBase\Repositories;
use App\Functions;
use App\ViewModels;
use KalnaBase\Utilities\Functions as Func;

//require_once UTILPATH . 'Registry.php';

/**
 * Description of ViewLoader
 *
 * @author:     Claus Hjort Bube <cb@kalna.dk>
 * @created:    17-10-2013
 * 
 * @name:       ViewLoader
 * @version:    0.1
 * @desc:       class for 
 * 
 * @param string $url
 */
class ViewLoader {

    protected $viewPath;
    protected $viewModelPath;
    protected $viewAssetsPath = VIEWASSETSPATH;
    protected $viewPrivateAssetsPath;
    public $viewModel;
    public $view;
    public $section;
    public $task;
    public $query;
    private $debug;
    private $registry;
    public $appConfig;
    private $url;
    private $authenticated;
    private $userName;
    protected $language;
    protected $vars = array();
    private $fileExtensions = array('.phtml', '.php', '.html', '.htm');
    private $standardHeadfile = 'HtmlHeader';
    private $standardPageHeaderfile = 'PageHeader';
    private $standardPageFooterfile = 'PageFooter';
    private $standardFootfile = 'HtmlFooter';
    private $standardView = 'Frontpage';
    private $standardSection = 'index';
    private $standardExtension = '.phtml';
    private $loginView = 'Login';
    private $loginSection = 'index';
    private $viewsWithOwnAssets = array('Time');
    private $viewsWithAccessControl = array('Time');

//    private $part = false;


    public function __construct($url) {
        require_once (UTIL_FUNC_PATH . 'getAllFunctions.php');
        $this->appConfig = System\AppConfig::getInstance();
        $this->registry = SessionRegistry::getInstance();
//        $includeAssets = false;
        $this->viewsWithAccessControl = $this->appConfig->values['view_access']['restricted'];

        $this->standardView = $this->appConfig->values['defaults']['view'];
//        var_dump($_REQUEST);
        /**
         * Security check
         * http://wblinks.com/notes/secure-session-management-tips
         */
        unset($_SESSION['login_msg']);
        //If ($_SESSION['_USER_LOOSE_IP'] != long2ip(ip2long(filter_input(INPUT_SERVER, 'REMOTE_ADDR')) & ip2long("255.255.0.0")) || $_SESSION['_USER_AGENT'] != filter_input(INPUT_SERVER, 'HTTP_USER_AGENT') || $_SESSION['_USER_ACCEPT_ENCODING'] != filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING') || $_SESSION['_USER_ACCEPT_LANG'] != filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE') || $_SESSION['_USER_ACCEPT_CHARSET'] != filter_input(INPUT_SERVER, 'HTTP_ACCEPT_CHARSET')) {
        If ($_SESSION['_USER_LOOSE_IP'] != long2ip(ip2long(filter_input(INPUT_SERVER, 'REMOTE_ADDR')) & ip2long("255.255.0.0")) || $_SESSION['_USER_AGENT'] != filter_input(INPUT_SERVER, 'HTTP_USER_AGENT') || $_SESSION['_USER_ACCEPT_ENCODING'] != filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING') || $_SESSION['_USER_ACCEPT_LANG'] != filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')) {
            // Destroy and start a new session
            session_unset(); // Same as $_SESSION = array();
            session_destroy(); // Destroy session on disk
            session_start();
            session_regenerate_id(true);

            // Set warning
            $_SESSION['login_msg_level'] = "alert-danger";
            $_SESSION['login_msg'] = "Possible session hijacking attempt.";
        }
//unset($_SESSION['login_msg']);
        // Store these values into the session so I can check on subsequent requests.
        $_SESSION['_USER_AGENT'] = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        $_SESSION['_USER_ACCEPT_ENCODING'] = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING');
        $_SESSION['_USER_ACCEPT_LANG'] = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
        $_SESSION['_USER_ACCEPT_CHARSET'] = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_CHARSET');
//            $_SESSION['login_msg'] = "Possible session hijacking attempt." . "<br>old: " . $_SESSION['_USER_ACCEPT'] . "<br>new: " .filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        // Only use the first two blocks of the IP (loose IP check). Use a
        // netmask of 255.255.0.0 to get the first two blocks only.
        $_SESSION['_USER_LOOSE_IP'] = long2ip(ip2long(filter_input(INPUT_SERVER, 'REMOTE_ADDR')) & ip2long("255.255.0.0"));
        /**
         * End of security check
         */
        $this->view = $this->standardView;
        $this->viewPath = VIEWPATH . DIRSEP . $this->view;
        $this->section = $this->standardSection;
        $this->debug = 1;
        if (!isset($url)) {
            $this->debug = 2;
        } else {
            $this->debug = 3;
            $this->url = $url;
            $urlArray = array_filter(explode("/", $url));

            $this->viewPath = VIEWPATH;
            $this->viewModelPath = VIEWMODEL_PATH;

//            print_r($urlArray);
            $this->extractSiteElements($urlArray);
        }

//        if ($includeAssets || in_array($this->view, $this->viewsWithOwnAssets)) {
//            $this->viewAssetsPath = $this->viewPath . '/Assets/';
//        }

        $action = $this->getSubmittedValues('action');
        $this->debug = $action;
        if (isset($action) and $action == "login") {
            $auth = new Functions\Authentication();
            $username = getSubmittedValues('username');
            $password = getSubmittedValues('password');
            $auth->login($username, $password);
            if ($auth->loginResult == 1) {
                $this->userType = $auth->userType;
                $this->authenticated = 1;
                $this->view = $this->userType;
                $this->section = $this->appConfig->values['defaults'][$this->userType]['section'];
                $this->viewPath = VIEWPATH . DIRSEP . $this->view;
                header("Location: http://" . $this->appConfig->values['application']['server'] . "/" . $this->appConfig->values['application']['path'] . "/" . $this->userType . '/' . $this->section . '/' . 500145);
            }
        } elseif (isset($action) and $action == "logout") {
//            require_once REPO_PATH . 'Auth.php';
            $auth = new Functions\Authentication();
            //dbms($key,'js');
            $auth->logout();
        }

        $accessCodes = $this->restrictedAccess($this->view, $this->section);
        $this->controlAccess($accessCodes);

        $this->viewPrivateAssetsPath = $this->viewPath . '/Assets/';

        $this->registry->currWebPath = '/T/' . $this->appConfig->values['application']['path'] . '/' . $this->view;
    }

    /**
     * 
     * @param array $urlArray
     */
    private function extractSiteElements($urlArray) {
//        if (!$urlArray[0] == "") {
        if (!$urlArray[0] == "" && !in_array($urlArray[0], $this->appConfig->values['special_items']['part_view']) && file_exists(VIEWPATH . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension)) {
            $this->view = $urlArray[0];
            array_shift($urlArray);
//            echo '$this->view; ' . $this->view . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
//            echo VIEWPATH . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension . "<br>";
        }
//        echo VIEWPATH . $this->view . DIRSEP . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension . "<br>";
        while (!$urlArray[0] == "" && !in_array($urlArray[0], $this->appConfig->values['special_items']['part_view']) && file_exists(VIEWPATH . $this->view . DIRSEP . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension)) {
            $this->viewPath = $this->viewPath . $this->view;
            $this->view = $urlArray[0];
            array_shift($urlArray);
//            echo '$this->view; ' . $this->view . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
//            echo VIEWPATH . $this->view . DIRSEP . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension . "<br>";
        }

        if (!$urlArray[0] == "" && in_array($urlArray[0], $this->appConfig->values['special_items']['part_view']) && file_exists(VIEWPARTPATH . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension)) {
            $this->viewPath = VIEWPARTPATH;
            $this->view = $urlArray[0];
            array_shift($urlArray);
//            echo '$this->view; ' . $this->view . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
//            echo VIEWPATH . $urlArray[0] . DIRSEP . $this->standardSection . $this->standardExtension . "<br>";
        }
//        }
        if (empty($this->view)) {
            $this->view = $this->appConfig->values['defaults']['view'];
        }

        $this->viewPath = $this->viewPath . DIRSEP . $this->view;
        $this->viewModelPath = $this->viewModelPath . DIRSEP . $this->view;

//        echo $this->viewPath;

        if (!$urlArray[0] == "" && file_exists($this->viewPath . DIRSEP . $urlArray[0] . $this->standardExtension)) {
            $this->section = $urlArray[0];
            array_shift($urlArray);
//            echo '$this->section; ' . $this->section . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
        } else {
            $this->section = Func\NullFunctions::nvl($this->appConfig->values['defaults'][$this->view]['section'], $this->standardSection);
//            echo '$this->section; ' . $this->section . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
        }
        if (!$urlArray[0] == "" && isset($urlArray[0])) {
            $this->task = $urlArray[0];
            array_shift($urlArray);
//            echo '$this->task; ' . $this->task . "<br>";
//            echo '$urlArray[0]; ' . $urlArray[0] . "<br>";
        } else {
            $this->task = 'none'; // Default Task
        }
        $this->query = $urlArray;
    }

    /**
     * 
     * @param string $view View name
     * @param string $section Section name - optional
     * @return int
     */
    private function restrictedAccess($view, $section = NULL) {
        $accessCodes = array(0);
        $restrictedViews = $this->appConfig->values['restricted_access'];
        $restrictedSection = array_key_exists($view, $restrictedViews) ? $this->appConfig->values['restricted_access'][$view] : NULL;
        if (!empty($restrictedSection) && array_key_exists($section, $restrictedSection)) {
            $accessCodes = array_values($restrictedSection[$section]);
        } elseif (!empty($restrictedViews) && array_key_exists($view, $restrictedViews)) {
            $accessCodes = array_values($restrictedViews[$view]);
        }
        return $accessCodes;
    }

    private function controlAccess($accessCodes) {
//        var_dump((string) $_SESSION['accessPin']);
//        var_dump($accessCodes);
//        var_dump(array_search((string) $_SESSION['accessPin'], $accessCodes));
        if (!$accessCodes[0] == 0) {
            if (!isset($_SESSION['login']) || $_SESSION['login'] == 0 || !isset($_SESSION['userId'])) {
//                $this->viewPath = $this->viewAssetsPath;
                $this->view = $this->appConfig->values['special_items']['login']['view'];
                $this->section = $this->appConfig->values['special_items']['login']['section'];
                $this->viewPath = VIEWPATH . DIRSEP . $this->view;
                $this->authenticated = 0;
                return NULL;
            } elseif (isset($_SESSION['accessPin']) && array_search((string) $_SESSION['accessPin'], $accessCodes) !== FALSE) {
                $this->authenticated = 1;
                return TRUE;
            } else {
                $this->view = $this->appConfig->values['special_items']['no_access']['view'];
                $this->section = $this->appConfig->values['special_items']['no_access']['section'];
                $this->viewPath = VIEWPATH . DIRSEP . $this->view;
                $this->authenticated = 0;
                return FALSE;
            }
        }
    }

    /**
     * Description of getSubmittedValues
     *
     * A function to get the submitted value
     * 
     * @author:     chb
     * 
     * @return:     
     * string, int, decimal
     * 
     * @version:    0.1
     * 
     * @param
     * $field required Variable containing the submitted value
     * 
     */
    function getSubmittedValues($field) {
        $return = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
        return $return;
    }

    /**
     * 
     * @param string $headFile Head filename
     * @throws Exception
     */
    public function renderHtmlHead($headFile = NULL) {
        if (!in_array($this->view, $this->appConfig->values['special_items']['part_view'])) {
            if ($headFile == NULL) {
                $headFile = $this->standardHeadfile . $this->standardExtension;
            }
            if (file_exists($this->viewPrivateAssetsPath . $headFile)) {
                include $this->viewPrivateAssetsPath . $headFile;
            } elseif (file_exists($this->viewAssetsPath . $headFile)) {
                include $this->viewAssetsPath . $headFile;
            } else {
                throw new Exception('no template file ' . $headFile . ' present in directory ' . $this->viewPrivateAssetsPath . ' or ' . $this->viewAssetsPath);
            }
        }
    }

    public function renderHeader($headerFile = NULL) {
        if (!in_array($this->view, $this->appConfig->values['special_items']['part_view'])) {
            if ($headerFile == NULL) {
                $headerFile = $this->standardPageHeaderfile . $this->standardExtension;
            }
            if (file_exists($this->viewPrivateAssetsPath . $headerFile)) {
                include $this->viewPrivateAssetsPath . $headerFile;
            } elseif (file_exists($this->viewAssetsPath . $headerFile)) {
                include $this->viewAssetsPath . $headerFile;
            } else {
                throw new Exception('no template file ' . $headerFile . ' present in directory ' . $this->viewPrivateAssetsPath . ' or ' . $this->viewAssetsPath);
            }
        }
    }

    /**
     * 
     * @param type $sectionFile
     * @throws Exception
     */
    public function render($sectionFile = NULL) {
        if (isset($this->view) && $sectionFile == NULL) {
//            echo $this->view.'/'.$this->section;
            $sectionFile = $this->section . $this->standardExtension;
        } elseif (!isset($this->view) && $sectionFile == NULL) {
            $sectionFile = $this->standardSection . $this->standardExtension;
        }
//            echo $sectionFile;

        $currViewParts = explode('.', $sectionFile);
        $currViewExtension = '.' . end($currViewParts);

        if (!in_array($currViewExtension, $this->fileExtensions)) {
            $sectionFile = $sectionFile . $this->standardExtension;
        }
//throw new Exception('No sectionfile present in directory ' . $this->viewPath . DIRSEP . ' - | - ' . 'View: ' . $this->view . ' - | -' . 'Section: ' . $this->section . ' (' . $sectionFile . ' ) ' . ' - |-' . 'Task: ' . $this->task . ' - | -' . 'Debug: ' . $this->debug);

        if (file_exists($this->viewPath . DIRSEP . $sectionFile)) {
            include $this->viewPath . DIRSEP . $sectionFile;
        } else {
            throw new Exception('No sectionfile present in directory ' . $this->viewPath . DIRSEP . ' - | - ' . 'View: ' . $this->view . ' - | -' . 'Section: ' . $this->section . ' (' . $sectionFile . ' ) ' . ' - |-' . 'Task: ' . $this->task . ' - | -' . 'Debug: ' . $this->debug);
        }
    }

    public function renderFooter($footerFile = NULL) {
        if (!in_array($this->view, $this->appConfig->values['special_items']['part_view'])) {
            if ($footerFile == NULL) {
                $footerFile = $this->standardPageFooterfile . $this->standardExtension;
            }
            if (file_exists($this->viewPrivateAssetsPath . $footerFile)) {
                include $this->viewPrivateAssetsPath . $footerFile;
            } elseif (file_exists($this->viewAssetsPath . $footerFile)) {
                include $this->viewAssetsPath . $footerFile;
            } else {
                throw new Exception('no template file ' . $footerFile . ' present in directory ' . $this->viewPrivateAssetsPath . ' or ' . $this->viewAssetsPath);
            }
        }
    }

    /**
     * 
     * @param string $footFile Head filename
     * @throws Exception
     */
    public function renderHtmlFoot($footFile = NULL) {
        if (!in_array($this->view, $this->appConfig->values['special_items']['part_view'])) {
            if ($footFile == NULL) {
                $footFile = $this->standardFootfile . $this->standardExtension;
            }
            if (file_exists($this->viewPrivateAssetsPath . $footFile)) {
                include $this->viewPrivateAssetsPath . $footFile;
            } elseif (file_exists($this->viewAssetsPath . $footFile)) {
                include $this->viewAssetsPath . $footFile;
            } else {
                throw new Exception('no template file ' . $footFile . ' present in directory ' . $this->viewPrivateAssetsPath . ' or ' . $this->viewAssetsPath);
            }
        }
    }

    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function __get($name) {
        return $this->vars[$name];
    }

}
