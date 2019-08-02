<?php
    $conn = new mysqli("localhost", "root", "", "chatDB");
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    session_start();

        //config//
    //delay between apache and mysql (free hosting XD)
    $delay=0;
    //write logout after x seconds of inactivity
    $user_timeout=15+$delay;
    //max char in message
    $max_char=1000;
    //max login lenght
    $login_len=15;
    //max password lenght
    $passw_len=20;
    //max chat name lenght
    $chat_len=30;
    //max chat password lenght 
    $chat_passw_len=20;
    //load chat x seconds back after logging in
    $pre_login_chat=600;
    //reload logging date after x seconds of inactivity 
    $logindate_reload=100+$delay;
        //config//





    if(isset($_SESSION["login"])){
        //logged user
        $login=$_SESSION["login"];
        //logged user id
        $user_id=$_SESSION["user_id"];
        //used chat id
        $chat_id=$_SESSION["chat_id"];
        //used chat
        $chat=$_SESSION["chat"];
    }
    //content.php
    $last_printed_user="";
    $last_printed="";


?>
