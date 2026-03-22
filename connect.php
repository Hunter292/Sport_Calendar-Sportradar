<?php
$host='Localhost';
$user='root';
$pass='';
$db='sport_calendar';
try{
    $connection= new PDO("mysql:host={$host};dbname={$db};charset=utf8",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>FALSE]);
}
catch(PDOException $e){
    exit('Server error');
}