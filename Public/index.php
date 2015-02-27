<?php

/**
 * Define a few const.
 */
defined('DIRSEP') or define('DIRSEP', DIRECTORY_SEPARATOR);
defined('WDIRSEP') or define('WDIRSEP', '/');

defined('SITEPATH') or define('SITEPATH', realpath(dirname(__FILE__) . DIRSEP . '..') . DIRSEP);
defined('APPPATH') or define('APPPATH', realpath(SITEPATH) . DIRSEP . 'App' . DIRSEP);
defined('VENDORPATH') or define('VENDORPATH', realpath(SITEPATH) . DIRSEP . 'vendor' . DIRSEP);
defined('BASEPATH') or define('BASEPATH', realpath(SITEPATH . '..') . DIRSEP);
defined('PUBLICPATH') or define('PUBLICPATH', BASEPATH . 'Public' . WDIRSEP);

defined('COREPATH') or define('COREPATH', VENDORPATH . 'KalnaBase' . DIRSEP);
defined('LIBPATH') or define('LIBPATH', COREPATH . 'PHPLibraries' . DIRSEP);
defined('LANGPATH') or define('LANGPATH', COREPATH . 'Language' . DIRSEP);
defined('SYSPATH') or define('SYSPATH', COREPATH . 'System' . DIRSEP);
defined('UTILPATH') or define('UTILPATH', COREPATH . 'Utilities' . DIRSEP);
defined('UTILINCLPATH') or define('UTILINCLPATH', UTILPATH . 'Includes' . DIRSEP);
defined('UTIL_INCL_PATH') or define('UTIL_INCL_PATH', UTILPATH . 'Includes' . DIRSEP);
defined('UTIL_FUNC_PATH') or define('UTIL_FUNC_PATH', UTILPATH . 'Functions' . DIRSEP);
defined('MODEL_PATH') or define('MODEL_PATH', APPPATH . 'Models' . DIRSEP);
defined('MODEL_CONF_PATH') or define('MODEL_CONF_PATH', APPPATH . 'build' . DIRSEP . 'conf' . DIRSEP);
defined('MODEL_CLASS_PATH') or define('MODEL_CLASS_PATH', APPPATH . 'build' . DIRSEP . 'classes' . DIRSEP);
defined('REPO_PATH') or define('REPO_PATH', COREPATH . 'Repositories' . DIRSEP);
defined('VIEWMODEL_PATH') or define('VIEWMODEL_PATH', APPPATH . 'ViewModels' . DIRSEP);
defined('VIEWPATH') or define('VIEWPATH', APPPATH . 'Views' . DIRSEP);
defined('VIEWPARTPATH') or define('VIEWPARTPATH', APPPATH . 'ViewParts' . DIRSEP);
defined('VIEWASSETSPATH') or define('VIEWASSETSPATH', VIEWPATH . 'Assets' . DIRSEP);


require_once (SITEPATH . 'vendor/autoload.php');
$appConfig = KalnaBase\System\AppConfig::getInstance();

defined('PUBLICBASEPATH') or define('PUBLICBASEPATH', WDIRSEP . $appConfig->values['application']['path'] . WDIRSEP);
defined('PUBLICHTMLPATH') or define('PUBLICHTMLPATH', PUBLICBASEPATH . 'Public' . WDIRSEP);

defined('HOMEVIEW') or define('HOMEVIEW', 'Home');

$url = $_GET['url'];
require_once (UTILPATH . 'shared.php');
require_once (UTIL_FUNC_PATH . 'getSubmittedValues.php');
/**
 * Check environment and set corresponding settings
 */
$config = KalnaBase\System\Config::getInstance();
$environment = $config->config_values['application']['environment'];
$environment_array = $config->config_values['environment'][$environment];
foreach ($environment_array as $setting => $value) {
    ini_set("$setting", "$value");
}
setlocale(LC_ALL, 'da_DK', 'da', 'danish', 'DK');
error_reporting($config->config_values['application']['error_reporting']);
//$urlArray = explode("/", $url);
//print_r($urlArray);
//if (isset($urlArray)) {
//    echo 'TRUE<br>';
//} else {
//    echo 'FALSE<br>';
//}
//echo '====<br>';
//echo $urlArray[0] . '<br>';
//echo '====<br>';
//echo UTILPATH . 'shared.php';
