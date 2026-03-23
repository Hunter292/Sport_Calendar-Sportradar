<?php
require('connect.php')?>
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
            <form action="<?=$_SERVER['PHP_SELF']?>" method="post" >
                <div class="flexbox">
                    <div>
                        <label>Date From</label>
                        <input type="date" name="date-begin">
                        <label>Sport</label>
                        <input type="text" name="sport" list="sports">
                        <label>Sort by</label>
                        <input type="text" name="sort" list="sorts">
                    </div>
                    <div>
                        <label>Date To</label>
                        <input type="date" name="date-end">
                        <label>Venue</label>
                        <input type="text" name="venue" list="venues">
                        <button id="extra">Additional filters</button>
                    </div>
                </div>
                <div id="extras">
                    <label>City</label>
                    <input type="text" name="city" list="cities">
                    <label>Country</label>
                    <input type="text" name="country" list="countries">
                    <label>Team</label>
                    <input type="text" name="team" list="teams">
                </div>
                <datalist id="sports">
                    <?php
                    $query=$connection->query("SELECT name from sport");
                    $results=$query->fetchAll();
                    foreach($results as $result) echo "<option value=\"{$result['name']}\">"
                    ?>
                </datalist> 
                <datalist id="cities">
                    <?php
                    $query=$connection->query("SELECT city from location");
                    $results=$query->fetchAll();
                    foreach($results as $result) echo "<option value=\"{$result['city']}\">"
                    ?>
                </datalist> 
                <datalist id="countries">
                    <?php
                    $query=$connection->query("SELECT DISTINCT country from location");
                    $results=$query->fetchAll();
                    foreach($results as $result) echo "<option value=\"{$result['country']}\">"
                    ?>
                </datalist> 
                <datalist id="teams">
                    <?php
                    $query=$connection->query("SELECT name from team");
                    $results=$query->fetchAll();
                    foreach($results as $result) echo "<option value=\"{$result['name']}\">"
                    ?>
                </datalist> 
                <datalist id="venues">
                    <?php
                    $query=$connection->query("SELECT name from venue");
                    $results=$query->fetchAll();
                    foreach($results as $result) echo "<option value=\"{$result['name']}\">"
                    ?>
                </datalist> 
                <datalist id="sorts">
                    <option value="date">
                    <option value="sport">
                    <option value="venue">
                    <option value="city">
                    <option value="country">

                </datalist>
                
            </br>
                <input type="submit" value="Show Events">
            </form>
            <?php
            //fetching event data
            if($_SERVER['REQUEST_METHOD']==='POST'){
                require('connect.php');
                $sql='SELECT event_id,date,time,venue.name as v_name,city,country,sport.name as s_name,competition.name as c_name
                FROM event JOIN competition ON competition.competition_id=event._competition_id join sport on sport.sport_id=event._sport_id 
                join venue ON venue.venue_id=event._venue_id join location on location.location_id=venue._location_id
                ';
                //add filters
                $where='';
                if($_POST["sport"]) $where.="sport.name=:sname";
                if($_POST["venue"]) $where.=" AND venue.name=:vname";
                if($_POST["country"])$where.=" AND country=:country";
                if($_POST["city"])$where.=" AND city=:city";
                if($_POST["team"])$where.=" AND event_id IN (SELECT event_id FROM teams_playing join team on team.team_id=teams_playing._team_id where team.name=:team)";
                if($_POST["date-begin"])$where.=" AND date>=:datebegin";
                if($_POST["date-end"])$where.=" AND date<=:dateend";
                if($where){
                    if($where[0]==' ')$sql.='where '.substr($where,5);
                    else $sql.='where '.$where;
                }
                $sort=$_POST["sort"];
                switch($sort){
                    case "date": $sql.=" order by date"; break;
                    case "sport": $sql.=" order by sport.name"; break;
                    case "venue": $sql.=" order by venue.name"; break;
                    case "city": $sql.=" order by city"; break;
                    case "country": $sql.=" order by country"; break;
                    default: break;
                }
                $query=$connection->prepare($sql);
                if($_POST["sport"]) $query->bindValue(':sname',$_POST["sport"],PDO::PARAM_STR);
                if($_POST["venue"]) $query->bindValue(':vname',$_POST["venue"],PDO::PARAM_STR);
                if($_POST["country"])$query->bindValue(':country',$_POST["country"],PDO::PARAM_STR);
                if($_POST["city"])$query->bindValue(':city',$_POST["city"],PDO::PARAM_STR);
                if($_POST["team"])$query->bindValue(':team',$_POST["team"],PDO::PARAM_STR);
                if($_POST["date-begin"])$query->bindValue(':datebegin',$_POST["date-begin"],PDO::PARAM_STR);
                if($_POST["date-end"])$query->bindValue(':dateend',$_POST["date-end"],PDO::PARAM_STR);
                
                $query->execute();
                $results=$query->fetchAll();
                //get a list of playing teams for each event
                $event_ids=[];
                for($i=0;$i<sizeof($results);$i++) array_push($event_ids,$results[$i]["event_id"]);
                require("get_players.php");
                $team_event=get_players($event_ids);
                //build result table
                ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr><th>Time</th><th>Sport</th><th>Venue</th><th>Competition</th><th>Teams</th></tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($results as $result){
                            $time=date('D',strtotime($result['date']))." {$result['date']}";
                            $teams=str_replace(';',"</br>",$team_event[$result['event_id']]);
                            if(strlen($teams)>100) $teams=substr($teams,0,100).'...';
                            echo "<tr><th>$time</br>".substr($result['time'],0,5)."</br><a href=\"single_event.php?e={$result['event_id']}\" >details</a></th><th>{$result['s_name']}</th><th>{$result['v_name']}</br>{$result['city']}</br>{$result['country']}</th>
                            <th>{$result['c_name']}</th><th>$teams</th></tr>";
                        }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            
            ?>
            </main>

        </div>
    </div>
    <script type="text/javascript" src="javascript.js"></script>
</body>
</html>