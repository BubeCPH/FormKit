<?php

namespace KalnaBase\Utilities;

$registry = SessionRegistry::getInstance();

  /** Secondary Call Function * */
function performAction($view, $action, $queryString = null, $render = 0) {
    $viewName = ucfirst($view) . 'Controller';
    $dispatch = new $viewName($view, $action);
    $dispatch->render = $render;
    return call_user_func_array(array($dispatch, $action), $queryString);
}

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

    $viewLoader = new ViewLoader($url);
    $languageLoader = LanguageLoader::getInstance('da_DK');
    $viewLoader->lang = $languageLoader->values;
    
    $viewLoader->renderHtmlHead();
    $viewLoader->renderHeader();
    $viewLoader->render();
    $viewLoader->renderFooter();
    $viewLoader->renderHtmlFoot();
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
