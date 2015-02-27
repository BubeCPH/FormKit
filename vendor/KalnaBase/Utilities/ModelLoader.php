<?php
// Include the main Propel script
require_once BASEPATH . 'vendor/propel/propel1/runtime/lib/Propel.php';

// Initialize Propel with the runtime configuration
Propel::init(MODEL_CONF_PATH . "time-conf.php");

// Add the generated 'classes' directory to the include path
set_include_path(MODEL_CLASS_PATH . PATH_SEPARATOR . get_include_path());

