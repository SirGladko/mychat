<?php
    $conn = new mysqli("localhost", "root", "", "chatDB");
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    session_start();

        //config//
    //delay między bazą danych a apache (42 sekundy na tym darmowym hostingu xD)
    $delay=0;
    //czas od ostatniej aktywności, do wylogowania (w sekundach)
    $user_timeout=15+$delay;
    //max liczba znaków w jednej wiadomości
    $max_char=1000;
    //max długość loginu
    $login_len=15;
    //max długość hasła
    $passw_len=20;
    //max długość nazwy czatu
    $chat_len=30;
    //max długość hasła czatu
    $chat_passw_len=20;
    //widoczność historii czatu w tył od zalogowania (w sekundach)
    $pre_login_chat=600;
    //przeładowanie daty logowania po danym czasie nieaktywności (w sekundach)
    $logindate_reload=100+$delay;
        //config//





    if(isset($_SESSION["login"])){
        //zalogowany użytkownik
        $login=$_SESSION["login"];
        //id zalogowanego usera
        $user_id=$_SESSION["user_id"];
        //id czatu na ktorym jestesmy zalogowani
        $chat_id=$_SESSION["chat_id"];
        //nazwa zalogowanego chatu
        $chat=$_SESSION["chat"];
    }
    //content.php
    $last_printed_user="";
    $last_printed="";


?>