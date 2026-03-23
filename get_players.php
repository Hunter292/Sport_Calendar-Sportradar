<?php
    // returns array of strings containig names of teams playing in each event, key=event_id
    function get_players(array $event_ids){
        global $connection;
        if(!$event_ids) return NULL;
        $id_list=$event_ids[0];
        for($i=1;$i<sizeof($event_ids);$i++){
            $id_list.=",$event_ids[$i]";
        }
        $query=$connection->query("SELECT _event_id,name,_team_id FROM teams_playing join team on team.team_id=teams_playing._team_id where _event_id in($id_list)");
        $teams=$query->fetchAll();
        $team_event=[];
        foreach($teams as $team){
            if(array_key_exists($team["_event_id"],$team_event)) $team_event[$team["_event_id"]].=";{$team['name']}";
            else $team_event[$team["_event_id"]]=$team['name'];
        }
        return $team_event;
    }
?>