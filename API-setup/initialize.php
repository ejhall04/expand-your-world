<?php

    defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
    defined('ROOT') ? null : define('ROOT', dirname(__DIR__));

    require_once('config.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'objectmodule.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'sessionmodule.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'stopmodule.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'tourmodule.php');
    require_once(ROOT . DS . 'persistence layer' . DS . 'usermodule.php');
?>