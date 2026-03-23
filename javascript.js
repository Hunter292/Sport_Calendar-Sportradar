function generate(){
    let num=document.getElementById("num_teams").value;
    let div=document.getElementById("teams_selection");
    div.innerHTML="";
    for( let i=0;i<num;i++){
        div.innerHTML+=" <label>Team"+(i+1)+"</label><input type=\"text\" name=\"team"+(i+1)+"\" list=\"teams\"> </br>";
    }
}
window.onload=function(){
    let elem=document.getElementById('extras')
    if(elem){
        elem.style.display="none";
        document.getElementById('extra').addEventListener('click',
        function(event){
            event.preventDefault();
            let elem= document.getElementById('extras');
            elem.style.display=="none"? elem.style.display="block":elem.style.display="none";
        }
        );
    }
}
function selected_winner(num){
    let elem=document.getElementById("team"+num).value;
    let id=document.getElementById("team"+num).name;
    if(elem) window.location.href="winner_addition.php?t="+elem+"&e="+id;
}