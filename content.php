<?php
    //podłączenie do bazy danych i podstawowe zmienne
    include('more/conf.php');

    //jeżeli używane konto jest zbanowane to wyloguj
    $sql = "SELECT user_flag from users where user_id='$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($row["user_flag"]==2){
        session_unset();
        $_SESSION["ban"]=1;
        echo "<script>window.location.href = 'login.php' </script>";
    }
    //sprawdzanie czasu ostatniej aktywnosci wszystkich użytkowników, którzy są przypisani jako aktywni na aktualnym czacie i ewentualne "wylogowanie" ich
    $sql = "SELECT user_id, user_flag, login, last_active FROM users WHERE chat_used='$chat_id';";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        //sprawdzenie czy nie jest nieaktywny dłużej niż timeout
        if (time()-strtotime($row["last_active"])>$user_timeout && $row["user_flag"]!=2){
            $loguser_id = $row["user_id"];
            $loguser_login = $row["login"];
            $logdate = $row["last_active"];
            //napisanie, że wyszedł z czatu, jeżeli nie jest aktywny
            $sql1 = "INSERT INTO records (user_id, chat_id, record_flag, text, record_date) values ('$loguser_id', '$chat_id', 3, '***$loguser_login OUT***', '$logdate');";
            $result1 = $conn->query($sql1);
            //usunięcie mu przypisania do czatu
            $sql1 = "UPDATE users SET chat_used = NULL WHERE user_id = '$loguser_id';";
            $result1 = $conn->query($sql1);
        }
    }
    //sprawdzanie przypisania aktualnego użytkownika do aktualnego czatu
    $sql = "SELECT chat_used, last_active FROM users WHERE user_id = '$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    //jeżeli nie jest do niego przypisany, to go przypisuje i wysyła wiadomość o dołączeniu
    if ($row["chat_used"]!=$chat_id){
        if (time()-strtotime($row["last_active"])>$logindate_reload){
            $sql = "UPDATE users SET chat_used = '$chat_id', date = current_timestamp WHERE user_id = '$user_id';";
        }
        else {
            $sql = "UPDATE users SET chat_used = '$chat_id' WHERE user_id = '$user_id';";
        }
        $result = $conn->query($sql);
        $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 2, '***$login IN***');";
        $result = $conn->query($sql);
    }
    //update czatu ostatniej aktywności
    $sql = "UPDATE users SET last_active = current_timestamp WHERE user_id = '$user_id';";
    $result = $conn->query($sql);
    //wypisywanie wszystkich wpisów
    $sql = "SELECT record_id, login, user_flag, chat_name, record_flag, text, record_date FROM ((records INNER JOIN users ON records.user_id = users.user_id) INNER JOIN chats ON records.Chat_id = chats.chat_id) WHERE chat_name = '$chat' ORDER BY record_date;";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        //wyświetla tylko wiadomości do kilku minut przed zalogowaniem sie na czat (patrz config)
        if (strtotime($_SESSION["logindate"])<strtotime($row["record_date"])+$pre_login_chat){
            //informacje do wyświetlania
            if ($_SESSION["permiss"]==1){
                $wpis=" (".$row["record_id"]."):</p>";
            }
            else {
                $wpis=":</p>";
            }
            //kolorki userów
            if ($row["user_flag"]==1){
                $recorduser="admin";
            }
            else {
                $recorduser="user";
            }
            //informacje o ostatnim wypisywanym użytkowniku
            if($last_printed_user==$row["login"]){
                $last_printed="<div class=\"wpis\"><div class=\"wciecie\"><p>";
            }
            else{
                $last_printed="<div class=\"wpis\"><p class=\"$recorduser\">".date('H:i', strtotime($row["record_date"]))." ".$row["login"].$wpis."<div class=\"wciecie\"><p>";
            }
            
            //wyświetlanie
            if ($row["record_flag"]==0){
                echo $last_printed.$row["text"]."</p></div></div>";
                $last_printed_user=$row["login"];
            }
            //info o dołączeniu
            else if ($row["record_flag"]==2){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p class=\"$recorduser\">".$row["login"]."<p> dołączył do czatu</p></div>";
            }
            //info o opuszczeniu
            else if ($row["record_flag"]==3){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p class=\"$recorduser\">".$row["login"]."<p> wyszedł z czatu</p></div>";
            }
            //wiadomosci spersonalizowane
            else if ($row["record_flag"]==4){
                if($row["login"]==$login){
                    $last_printed_user="";
                    echo "<div class=\"personal wpis\"><p>".$row["text"]."</p></div>";
                }
            }
            //wiadomość o zablokowaniu i odblokowaniu użytkownika
            else if ($row["record_flag"]==5){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p>".$row["text"]."</p></div>";
            }
            //wpisy usunięte
            else if ($row["record_flag"]==6){
                $last_printed_user="";
                echo "<div class=\"wpis\"><p class=\"$recorduser\">".date('H:i', strtotime($row["record_date"]))." ".$row["login"].$wpis."<br><p class=\"wciecie\">Ten wpis został usunięty</p></div>";
            }
            //wiadomosc o komendach
            else if ($row["record_flag"]==7){
                if($row["login"]==$login){
                    $last_printed_user="";
                    echo "<div class=\"personal wpis\"><p></p><div class=class=\"personal\"><p>Spis Komend</p></div>
                    <div class=\"personal\"><p>/ban (nick)           -ban</p></div>
                    <div class=\"personal\"><p>/unban (nick)         -unban</p></div>
                    <div class=\"personal\"><p>/del (nr. wpisu)      -usuwa wpis o podanym numerze</p></div>
                    <div class=\"personal\"><p>/permiss (nick) (1/0) -ustawia uprawnienia użytkownika/admina</p></div>
                    <div class=\"personal\"><p>/echo (nr. wpisu)     -pokazuje treść usuniętego wpisu</p></div><p></p></div>";
                }
            }
        }
    } 
?>