<?php
require('check-log.php');
require('connect.php');
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
            <h2>Add Event</h2>
            <?php
                //get data of matches that have been played and have no selected winner
                $sql='SELECT event_id,date,time,venue.name as v_name,city,country,sport.name as s_name
                FROM event JOIN competition ON competition.competition_id=event._competition_id join sport on sport.sport_id=event._sport_id 
                join venue ON venue.venue_id=event._venue_id join location on location.location_id=venue._location_id
                where status="played" and _winner IS NULL';
                $query=$connection->query($sql);
                $results=$query->fetchAll();
                $event_ids=[];
                for($i=0;$i<sizeof($results);$i++) array_push($event_ids,$results[$i]["event_id"]);
                require("get_players.php");
                $team_event=get_players($event_ids);
            ?>
             <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr><th>Time</th><th>Sport</th><th>Venue</th><th>Choose winner</th><th>Teams</th></tr>
                    </thead>
                    <tbody>
                    <?php
                        $i=0;
                        //print match data
                        foreach($results as $result){
                            $time=date('D',strtotime($result['date']))." {$result['date']}";

                            $teams=str_replace(';','</br>',$team_event[$result['event_id']]);
                            if(strlen($teams)>100) $teams=substr($teams,0,100).'...';
                            $team_names=explode(";",$team_event[$result['event_id']]);
                            echo "<tr><th>$time</br>".substr($result['time'],0,5)."</th><th>{$result['s_name']}</th><th>{$result['v_name']}</br>{$result['city']}</br>{$result['country']}</th>
                            <th> <input type=\"text\" id=\"team$i\" name=\"{$result["event_id"]}\" list=\"teams$i\"><button onclick=\"selected_winner($i)\">Add winner</button></th><th>$teams</th></tr>";
                            //print argument list for team input
                            echo"<datalist id=\"teams$i\">";
                            for($i=0;$i<sizeof($team_names);$i++) echo"<option value=\"$team_names[$i]\">";
                            echo"</datalist>";
                            $i++;
                        }
                    ?>
                    </tbody>
                </table>
            </main>

        </div>
    </div>
    <script type="text/javascript" src="javascript.js"></script>
</body>
</html>