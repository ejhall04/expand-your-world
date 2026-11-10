<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once 'initialize.php';

//instantiate object
$object = new Objects($db);

//object query
$result = $object->list();

$num = $result->rowCount();

if($num>0){
    $object_arr = array();
    $object_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $object_item = array(
            'object_id' => $object_id,
            'name' => $name,
            'description' => $description,
            'extended_description' => $extended_description
        );
        array_push($object_arr['data'], $object_item);
    }
    //convet to json and output
    echo json_encode($object_arr);
}else{
    echo json_encode(array('message' => 'No objects found.'));
}