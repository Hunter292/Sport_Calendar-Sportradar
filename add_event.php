<?php
require('connect.php');
if($_SERVER["REQUEST_METHOD"]==="POST"){
    //validate input data
    if(!strtotime($_POST["date"])) $e="<p>Incorrect date</p>";
    if(!strtotime($_POST["time"])) $e="<p>Incorrect time</p>";

    $query=$connection->prepare("SELECT sport_id from sport where name=:name");
    $query->execute(['name'=>$_POST['sport']]);
    $sport_id=$query->fetch();
    if(!$sport_id) $e="<p>Incorrect sport</p>";

    $query=$connection->prepare("SELECT venue_id from venue where name=:name");
    $query->execute(['name'=>$_POST['venue']]);
    $venue_id=$query->fetch();
    if(!$venue_id) $e="<p>Incorrect venue</p>";

    $query=$connection->prepare("SELECT competition_id from competition where name=:name");
    $query->execute(['name'=>$_POST['competition']]);
    $competition_id=$query->fetch();
    if(!$competition_id) $e="<p>Incorrect competition</p>";

    if(!isset($_POST["team1"])) $e="<p>Incorrect team selection</p>";
    else{
        $teams=[];
        $i=1;
        while(isset($_POST["team$i"])){
            $teams[] = $_POST["team$i"];
            $i++;
        }
        $placeholders=implode(',',array_fill(0,count($teams),'?'));
        $query=$connection->prepare("SELECT team_id FROM team WHERE name IN ($placeholders)");
        $query->execute($teams);
        $t_results=$query->fetchAll();
        $team_ids=[];
        foreach($t_results as $t_result) array_push($team_ids,$t_result["team_id"]);
        if(!sizeof($team_ids)==$i-1) $e="<p>Incorrect team selection2</p>";
    }
    if(!isset($e)){
        //insert event, data validated
        strtotime($_POST["date"])>=time()?$status="scheduled":$status="played";
        $query=$connection->query("INSERT INTO event values(NULL,{$sport_id["sport_id"]},{$venue_id["venue_id"]},{$competition_id["competition_id"]},NULL,'{$_POST["date"]}','{$_POST["time"]}:00','$status')");
        if(!$query) $e="<p>Something went wrong</p>";
        else{
            $insert="";
            $query=$connection->query("SELECT event_id FROM event WHERE _venue_id={$venue_id["venue_id"]} and _sport_id={$sport_id["sport_id"]} and date='{$_POST["date"]}' and time='{$_POST["time"]}:00' and _competition_id={$competition_id["competition_id"]}");
            $id=$query->fetch();
            $id=$id["event_id"];
            foreach($team_ids as $team_id) $insert.="(NULL,$id,$team_id,NULL),";
            $insert[strlen($insert)-1]=';';
            $query=$connection->query("INSERT INTO teams_playing VALUES $insert");
        }
    }
}

?>
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
        <input type="number" id="num_teams"> <button onclick="generate()">Generate input</button>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post" >
            <label>Date</label>
            <input type="date" name="date"> </br>
            <label>Time</label>
            <input type="time" name="time"> </br>
            <label>Sport</label>
            <input type="text" name="sport" list="sports"> </br>
            <label>Venue</label>
            <input type="text" name="venue" list="venues"> </br>
            <label>Competition</label>
            <input type="text" name="competition" list="competitions">
           
            <div id="teams_selection"></div>
            <datalist id="sports">
                <?php
                $query=$connection->query("SELECT name from sport");
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
            <datalist id="teams">
                <?php
                $query=$connection->query("SELECT name from team");
                $results=$query->fetchAll();
                foreach($results as $result) echo "<option value=\"{$result['name']}\">"
                ?>
            </datalist> 
            <datalist id="competitions">
                <?php
                $query=$connection->query("SELECT name from competition");
                $results=$query->fetchAll();
                foreach($results as $result) echo "<option value=\"{$result['name']}\">"
                ?>
            </datalist> 
            
        </br>
            <input type="submit" value="Add event">
            <?=isset($e)?$e:''?>
        </form>
        </main>

    </div>
    <script type="text/javascript" src="javascript.js"></script>
</body>
</html>