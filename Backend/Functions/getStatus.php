<?php
function getStatusID($status, $db)
{
    $S_id='SELECT Status_id FROM status WHERE House_status=:status';
    $result=$db->prepare($S_id);
    $result->bindParam(":status",$status_id);
    $result->execute();
    $status_id=$result->fetchColumn();

    
    return $status_id;
    
}