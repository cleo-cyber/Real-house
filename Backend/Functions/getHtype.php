<?php
function houseType($house_type, $db)
{
    $h_type='SELECT housetype_id FROM house_type WHERE house_type=:house_type';
    $result=$db->prepare($h_id);
    $result->bindParam(":house_type",$house_type);
    $result->execute();
    $house_id=$result->fetchColumn();

    
    return $house_id;
    
}