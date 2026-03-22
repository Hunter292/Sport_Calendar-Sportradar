<?php
require('connect.php')?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sport Calendar</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main (2).css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
</head>

<body>
    <div class="container">

        <header>
            <h1>Sport Calendar</h1>
            <h3>All sport events in one place</h3>
        </header>
        <main>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post" >
            <label>Date From</label>
            <input type="date" name="date-begin">
            <label>Date To</label>
            <input type="date" name="date-end">
            <label>Sport</label>
            <input type="text" name="sport" list="sports">
            <label>City</label>
            <input type="text" name="city" list="cities">
            <label>Country</label>
            <input type="text" name="country" list="countries">
            <label>Team</label>
            <input type="text" name="team" list="teams">
            <label>Sort by</label>
            <input type="text" name="sort" list="sorts">
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
            <datalist id="sorts">
                <option value="date">
                <option value="sport">
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
                case "city": $sql.=" order by city"; break;
                case "country": $sql.=" order by country"; break;
                default: break;
            }
            $query=$connection->prepare($sql);
            if($_POST["sport"]) $query->bindValue(':sname',$_POST["sport"],PDO::PARAM_STR);
            if($_POST["country"])$query->bindValue(':country',$_POST["country"],PDO::PARAM_STR);
            if($_POST["city"])$query->bindValue(':city',$_POST["city"],PDO::PARAM_STR);
            if($_POST["team"])$query->bindValue(':team',$_POST["team"],PDO::PARAM_STR);
            if($_POST["date-begin"])$query->bindValue(':datebegin',$_POST["date-begin"],PDO::PARAM_STR);
            if($_POST["date-end"])$query->bindValue(':dateend',$_POST["date-end"],PDO::PARAM_STR);
            
            $query->execute();
            $results=$query->fetchAll();
            //get a list of playing teams for each event
            $id_list=$results[0]['event_id'];
            for($i=1;$i<sizeof($results);$i++){
                $id_list.=",{$results[$i]['event_id']}";
            }
            $query=$connection->query("SELECT _event_id,name FROM teams_playing join team on team.team_id=teams_playing._team_id where _event_id in($id_list)");
            $teams=$query->fetchAll();
            $team_event=[];
            foreach($teams as $team){
                if(array_key_exists($team["_event_id"],$team_event)) $team_event[$team["_event_id"]].="</br> {$team['name']}";
                else $team_event[$team["_event_id"]]=$team['name'];
            }
            //build result table
            ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr><th>Time</th><th>Sport</th><th>Venue</th><th>Competition</th><th>Teams</th></tr>
                </thead>
                <tbody>
                <?php
                    foreach($results as $result){
                        $time=date('F',strtotime($result['date']))." {$result['date']} : {$result['time']}";
                        $teams=$team_event[$result['event_id']];
                        if(strlen($teams)>100) $teams=substr($teams,0,100).'...';
                        echo "<tr> <th>$time</th><th>{$result['s_name']}</th><th>{$result['v_name']}</br>{$result['city']}</br>{$result['country']}</th>
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
</body>
</html>