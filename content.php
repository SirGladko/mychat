<?php
    //db connection and basic variables
    include('more/conf.php');

    //if user is banned then log him out
    $sql = "SELECT user_flag from users where user_id='$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($row["user_flag"]==2){
        session_unset();
        $_SESSION["ban"]=1;
        echo "<script>window.location.href = 'login.php' </script>";
    }
    //this is the part to logout and unbind from chat, users that are idle for given amount of time (every user bound to chat is visible on userlist)
    //check last active date for every user that is assigned to your chat and log them out if its expired
    $sql = "SELECT user_id, user_flag, login, last_active FROM users WHERE chat_used='$chat_id';";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        //check if user was active recently
        if (time()-strtotime($row["last_active"])>$user_timeout && $row["user_flag"]!=2){
            $loguser_id = $row["user_id"];
            $loguser_login = $row["login"];
            $logdate = $row["last_active"];
            //write that user left the chat
            $sql1 = "INSERT INTO records (user_id, chat_id, record_flag, text, record_date) values ('$loguser_id', '$chat_id', 3, '***$loguser_login OUT***', '$logdate');";
            $result1 = $conn->query($sql1);
            //unbind him from the chat
            $sql1 = "UPDATE users SET chat_used = NULL WHERE user_id = '$loguser_id';";
            $result1 = $conn->query($sql1);
        }
    }
    //check if you are assigned to your chat
    $sql = "SELECT chat_used, last_active FROM users WHERE user_id = '$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    //if not, then assign and write "enter the chat" message
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
    //update last active date
    $sql = "UPDATE users SET last_active = current_timestamp WHERE user_id = '$user_id';";
    $result = $conn->query($sql);
    //write chat content
    $sql = "SELECT record_id, login, user_flag, chat_name, record_flag, text, record_date FROM ((records INNER JOIN users ON records.user_id = users.user_id) INNER JOIN chats ON records.Chat_id = chats.chat_id) WHERE chat_name = '$chat' ORDER BY record_date;";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        //write messages to few minutes before logging in (look in conf.php)
        if (strtotime($_SESSION["logindate"])<strtotime($row["record_date"])+$pre_login_chat){
            //additional variables
            if ($_SESSION["permiss"]==1){
                $wpis=" (".$row["record_id"]."):</p>";
            }
            else {
                $wpis=":</p>";
            }
            //nicknames colors
            if ($row["user_flag"]==1){
                $recorduser="admin";
            }
            else {
                $recorduser="user";
            }
            //it is because i didnt want to repeat nickname before messages if one person is writing few of them in a row
            if($last_printed_user==$row["login"]){
                $last_printed="<div class=\"wpis\"><div class=\"wciecie\"><p>";
            }
            else{
                $last_printed="<div class=\"wpis\"><p class=\"$recorduser\">".date('H:i', strtotime($row["record_date"]))." ".$row["login"].$wpis."<div class=\"wciecie\"><p>";
            }
            
            //echo messages
            if ($row["record_flag"]==0){
                echo $last_printed.$row["text"]."</p></div></div>";
                $last_printed_user=$row["login"];
            }
            //info about logging in
            else if ($row["record_flag"]==2){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p class=\"$recorduser\">".$row["login"]."<p> entered the chat</p></div>";
            }
            //info about logging out
            else if ($row["record_flag"]==3){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p class=\"$recorduser\">".$row["login"]."<p> left the chat</p></div>";
            }
            //personalized messages
            else if ($row["record_flag"]==4){
                if($row["login"]==$login){
                    $last_printed_user="";
                    echo "<div class=\"personal wpis\"><p>".$row["text"]."</p></div>";
                }
            }
            //ban and unban messages
            else if ($row["record_flag"]==5){
                $last_printed_user="";
                echo "<div class=\"login wpis\"><p>".$row["text"]."</p></div>";
            }
            //deleted records
            else if ($row["record_flag"]==6){
                $last_printed_user="";
                echo "<div class=\"wpis\"><p class=\"$recorduser\">".date('H:i', strtotime($row["record_date"]))." ".$row["login"].$wpis."<br><p class=\"wciecie\">This message was deleted</p></div>";
            }
            //command list
            else if ($row["record_flag"]==7){
                if($row["login"]==$login){
                    $last_printed_user="";
                    echo "<div class=\"personal wpis\"><p></p><div class=class=\"personal\"><p>Command list</p></div>
                    <div class=\"personal\"><p>/ban (nick)           -ban</p></div>
                    <div class=\"personal\"><p>/unban (nick)         -unban</p></div>
                    <div class=\"personal\"><p>/del (record id)      -delete message with this id</p></div>
                    <div class=\"personal\"><p>/permiss (nick) (1/0) -set user/admin permissions</p></div>
                    <div class=\"personal\"><p>/echo (record id)     -show content of deleted message</p></div><p></p></div>";
                }
            }
        }
    } 
?>
