<?php 
    //db connection and basic variables
    include('more/conf.php');

    //get user flag again
    $sql = "SELECT user_flag from users where user_id='$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    //user flag
    $_SESSION["permiss"] = $row["user_flag"];
    //chat color
    if ($_SESSION["permiss"]==1){
        $user="\"admin\"";
    }
    else {
        $user="\"user\"";
    }
    echo "<p>Hello <p class=".$user.">".$login."</p> on chat "."<p class=\"user\">".$_SESSION["chat"]."</p></p>";
?>
