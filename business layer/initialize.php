<?php

    defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
    defined('ROOT') ? null : define('ROOT', '..');
    defined('BUSINESS_PATH') ? null : define('BUSINESS_PATH', ROOT . DS . 'business Layer');

    require_once(BUSINESS_PATH . DS . 'config.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'objectmodule.php');
?>