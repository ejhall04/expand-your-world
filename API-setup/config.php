<?php
    header("Content-Type: application/json; charset=UTF-8");
    $host = "127.0.0.1";
    $db_name = "expand_your_world";
    $username = "root";
    $password = "";


    $db = new PDO('mysql:host='.$host.';dbname='.$db_name.';charset=utf8',$username,$password);

    //set some attributes
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(Pdo\Mysql::ATTR_USE_BUFFERED_QUERY, true);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    define('APP_NAME', 'Expand Your World');


?>