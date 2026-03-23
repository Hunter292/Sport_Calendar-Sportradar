<?php
require('check-log.php');
require('connect.php');
if($_SERVER["REQUEST_METHOD"]==="GET"){
    $team=$_GET["t"];
    $id=$_GET["e"];
    $query=$connection->prepare("SELECT team_id FROM team where name=:name AND team_id IN(SELECT _team_id FROM teams_playing where _event_id=:id)");
    $query->execute(["name"=>$team,"id"=>$id]);
    $result=$query->fetch();
    if($result){
        $query=$connection->prepare("UPDATE event SET _winner={$result["team_id"]} WHERE event_id=:id");
        $query->execute(["id"=>$id]);
    }
}
header("Location:add_winner.php");
exit();
?>