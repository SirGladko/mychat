<?php
    //db connection and basic variables
    include('more/conf.php');

    if(isset($_POST["logout"])){
        //logout
        $sql = "UPDATE users SET chat_used = NULL WHERE user_id = '$user_id';";
        $result = $conn->query($sql);
        $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 3, '***$login OUT***');";
        $result = $conn->query($sql);
        session_unset();
    }
?>
