<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once 'initialize.php';

//instantiate object
$object = new Objects($db);

$object_id = isset($_GET['object_id']) ? $_GET['object_id'] : die();
$row = $object->read($object_id);

$object_arr = array(
    'name' => $row['name'],
    'description' => $row['description'],
    'extended_description' => $row['extended_description']
);

print_r(json_encode($object_arr));