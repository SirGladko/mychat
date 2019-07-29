<?php
    //podłączenie do bazy danych i podstawowe zmienne
    include('more/conf.php');
    
    //wypisuje wszystkich użytkowników przypisanych do aktualnego czatu 
    $sql="SELECT login, user_flag FROM users WHERE chat_used='$chat_id' ORDER BY user_flag DESC, login;";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        //kolorek
        if ($row["user_flag"]==1){
            $userwpis="<div class=\"onlineuser admin\">";
        }
        else {
            $userwpis="<div class=\"onlineuser user\">";
        }
        //wypisanie
        if ($row["user_flag"]!=2){
            echo $userwpis.$row["login"]."</div>";
        }
    }
?>