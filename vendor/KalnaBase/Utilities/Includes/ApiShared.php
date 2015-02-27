<?php

namespace KalnaBase\Utilities\Includes;

require_once (SYSPATH . 'Config.php');
require_once UTILPATH . 'SessionRegistry.php';
require_once UTILPATH . 'ApiLoader.php';
$registry = SessionRegistry::getInstance();
/** Set date SESSIONS variables * */
if (is_null($registry->get('date'))) {
    $registry->set('date', date("d-m-Y"));
    list($date_d, $date_m, $date_y) = explode("-", date("d-m-Y"));
    $registry->set('date_d', $date_d);
    $registry->set('date_m', $date_m);
    $registry->set('date_y', $date_y);
}


list($d, $m, $y) = explode("-", $registry->get('date'));
$registry->set('sql_date', $y . "-" . $m . "-" . $d);
//$sql_date = $registry->get('sql_date');

/** Check if environment is development and display errors * */
/**
 * Is now sat at the index.php file

  function setReporting() {
  if (DEVELOPMENT_ENVIRONMENT == true) {
  error_reporting(E_ALL);
  ini_set('display_errors','On');
  } else {
  error_reporting(E_ALL);
  ini_set('display_errors','Off');
  ini_set('log_errors', 'On');
  ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
  }
  }
 * 
 */
/** Check for Magic Quotes and remove them * */
/*
  function stripSlashesDeep($value) {
  $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
  return $value;
  }

  function removeMagicQuotes() {
  if (get_magic_quotes_gpc()) {
  $_GET = stripSlashesDeep($_GET);
  $_POST = stripSlashesDeep($_POST);
  $_COOKIE = stripSlashesDeep($_COOKIE);
  }
  }
 * 
 */

/** Check register globals and remove them * * /
  function unregisterGlobals() {
  if (ini_get('register_globals')) {
  //$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
  $array = array('_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
  foreach ($array as $value) {
  foreach ($GLOBALS[$value] as $key => $var) {
  if ($var === $GLOBALS[$key]) {
  unset($GLOBALS[$key]);
  }
  }
  }
  }
  }

  /** Secondary Call Function * */
function performAction($view, $action, $queryString = null, $render = 0) {

    $viewName = ucfirst($view) . 'Controller';
    $dispatch = new $viewName($view, $action);
    $dispatch->render = $render;
    return call_user_func_array(array($dispatch, $action), $queryString);
}

/** Routing * */
//function routeURL($url) {
//    global $routing;
//
//    foreach ($routing as $pattern => $result) {
//        if (preg_match($pattern, $url)) {
//            return preg_replace($pattern, $result, $url);
//        }
//    }
//
//    return ($url);
//}

/** Main Call Function * */
function callHook() {
    global $url;
    
    function array_in_str($fString, $fArray) {
        foreach ($fArray as $Value) {
            $Pos = stripos($fString, $Value);
            if ($Pos !== false) {
                // Add whatever information you need
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    $viewLoader = new ApiLoader($url);
    $viewLoader->renderHtmlHead();
    $viewLoader->render();
}

/** GZip Output * */
function gzipOutput() {
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ') || false !== strpos($ua, 'Opera')) {
        return false;
    }

    $version = (float) substr($ua, 30);
    return (
            $version < 6 || ($version == 6 && false === strpos($ua, 'SV1'))
            );
}

/** Get Required Files * */
gzipOutput() || ob_start("ob_gzhandler");


//$cache = & new Cache();
//$inflect = & new Inflection();
//setReporting();
//removeMagicQuotes();
//unregisterGlobals();
callHook();
?>