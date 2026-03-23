<?php
session_start();
if($_SERVER["REQUEST_METHOD"]==="POST"){
    require("connect.php");
    $query=$connection->prepare("SELECT password FROM user WHERE login=:login");
    $query->execute(["login"=>$_POST["login"]]);
    $result=$query->fetch();
    if($result && password_verify($_POST["password"],$result["password"])){
        $_SESSION["logged"]=TRUE;
        if(isset($_SESSION["redir"])) header("Location:{$_SESSION["redir"]}");
        else header("Location:index.php");
        exit();
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

    <link rel="stylesheet" href="main.css">
</head>

<body>
    <div id="wrapper">
        <div class="container">
            <main>
                <div>
                <form action="<?=$_SERVER['PHP_SELF']?>" method="post" >
                    <label>Login</label>
                    <input type="text" name="login">
                    <label>Password</label>
                    <input type="password" name="password"> </br>
                    <input type="submit" value="Log in">
                </form>
                </div>
            </main>

        </div>
    </div>
</body>
</html>