<?php
if(!$_SERVER["REQUEST_METHOD"]==="GET" || !isset($_GET["e"])){
    header("Location:index.php");
    exit();
}
require('connect.php');
//get event data
$event_id=$_GET["e"];
$sql='SELECT event_id,date,time,venue.name as v_name,city,country,sport.name as s_name,competition.name as c_name, status,description,capacity,address,_winner
            FROM event JOIN competition ON competition.competition_id=event._competition_id join sport on sport.sport_id=event._sport_id 
            join venue ON venue.venue_id=event._venue_id join location on location.location_id=venue._location_id where event_id=:event';
$query=$connection->prepare($sql);
$query->execute(["event"=>$event_id]);
$result=$query->fetch();
if(!$result){
    header("Location:index.php");
    exit();
}
//get teams that play the event
$query=$connection->prepare("SELECT name,city,country,team_id FROM teams_playing join team on teams_playing._team_id=team.team_id LEFT JOIN location on location.location_id=team._location_id WHERE _event_id=:event");
$query->execute(["event"=>$event_id]);
$teams=$query->fetchAll();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sport Calendar</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
</head>

<body>
    <div id="wrapper">
        <?php include("header.html")?>
        <div class="container">
            <main>
                <div>
                    <div><h3>Details</h3><div><p>Date: <?=$result["date"]?></p><p>Time: <?=substr($result["time"],0,5)?></p><p>Sport: <?=$result["s_name"]?></p><p>Competition: <?=$result["c_name"]?></p></div></div>
                    <div><h3>Teams/Participants</h3><div>
                    <?php
                        foreach($teams as $team){
                            if($team['team_id']==$result['_winner']) echo"<h3 style=\"color:#36b03c\">Winner</h3>";
                            echo"<p>{$team['name']}</br>{$team['city']} {$team['country']}</p>";
                        }
                    ?>
                    </div></div>
                    <div><h3>Venue</h3><div><p>Address: <?=$result["address"]?></p><p>City: <?=$result["city"]?></p><p>Country: <?=$result["country"]?></p><p>Capacity: <?=$result["capacity"]?></p></div></div>
                    <div><h3>Description:</h3><div><p class="long-text"><?=$result["description"]?></p></div></div>

                    
                </div>
            </main>

        </div>
    </div>
</body>
</html>