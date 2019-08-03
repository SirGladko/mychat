<?php 
    //db connection and basic variables
    include('more/conf.php');

    $loginErr="";
    $chatErr="";
    if (isset($_POST["submitek"])){
        //check entered chat
        if (isset($_POST["chat"])){
            //if its not too long
            if(strlen($_POST["chat"])>$chat_len){
                $chatErr="Chat name is too long";
            }
            else{
                //default chat
                if (strlen($_POST["chat"])==0){
                    $chat="default";
                    $chatpassw="";
                }
                //entered chat
                else{
                    $chat=htmlspecialchars(addslashes($_POST["chat"]));
                    $chatpassw=addslashes($_POST["chatpassw"]);
                }
                //check if entered chat exist
                $sql = "SELECT chat_id, chat_name, chat_password from chats where chat_name='$chat';";
                $result = $conn->query($sql);
                if ($result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    //if password isnt too long
                    if(strlen($chatpassw)>$chat_passw_len){
                        $chatErr="Chat password is too long";
                    }
                    //check if password is correct
                    else{
                        if ($row["chat_password"]==$chatpassw){
                            $chat=$row["chat_name"];
                            $chat_id=$row["chat_id"];
                        }
                        else{
                            $chatErr="Chat password isnt correct";
                        }
                    }
                }
                //create new chat if it doesnt exist
                else{
                    $sql = "INSERT INTO chats (chat_name, chat_password, chat_flag) values ('$chat', '$chatpassw', 0);";
                    $result = $conn->query($sql);
                    $result = $conn->query("select last_insert_id();");
                    $row = $result->fetch_assoc();
                    $chat_id=$row["last_insert_id()"];
                }
            }
        }
        
        $login=addslashes($_POST["login"]);
        $password=addslashes($_POST["passw"]);
        //check login and password
        if (strlen($login)==0 or strlen($login)>$login_len){
            $loginErr="login or password incorrect";
        }
        else{
            $sql = "SELECT user_id, login, password, user_flag from users where login='$login';";
            $result = $conn->query($sql);
            if ($result->num_rows == 1){
                $row = $result->fetch_assoc();
                //if password is correct
                if ($row["login"]==$login && $row["password"]==$password){
                    //if account is banned
                    if ($row["user_flag"]==2){
                        $loginErr="Account is banned";
                    }
                    else{
                        //if chat is without error
                        if ($chatErr==""){
                            $_SESSION["login"]=$row["login"];
                            $_SESSION["user_id"]=$row["user_id"];
                            $_SESSION["chat"]=$chat;
                            $_SESSION["chat_id"]=$chat_id;

                            $user_id = $row["user_id"];
                            //update login date and mark used chat
                            $sql = "UPDATE users SET date = current_timestamp, last_active = current_timestamp, chat_used = '$chat_id' WHERE user_id = '$user_id';";
                            $result = $conn->query($sql);
                            //info about used chat
                            $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 2, '***$login IN***');";
                            $result = $conn->query($sql);
                        }
                    }
                }
                else{
                    $loginErr="login or password incorrect";
                }
            }
            else{
                $loginErr="login or password incorrect";
            }
        }
    }

    //if login session isnt empty, go to index
    if (!empty($_SESSION["login"])){
        header("location: /");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="more/style.css">
        <link rel="stylesheet" href="more/bootstrap.css">
        <link rel="stylesheet" href="more/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
        </script>
    </head>
    <body>
        <?php
            //if he was banned, alert message
            if (isset($_SESSION["ban"])){
                if ($_SESSION["ban"]==1){
                    echo "<script>alert(\"Your account was banned\");</script>";
                    $_SESSION["ban"]=0;
                }
            }
        ?>
        <div class="container">
            <div class="col-lg-12 centered">
                <div class="loginbox">
                    <div class="login">
                        <form action="" method="POST">
                            <input class="logininput" name="login" placeholder="Login" type="text" maxlength="<?php echo $login_len;?>" required autofocus>
                            <input class="logininput" name="passw" placeholder="Password" type="password" maxlength="<?php echo $passw_len;?>" required>
                            <div class="mg-b-20 error"><?php echo $loginErr ?></div>
                            <div class="tooltipx">
                                <img class="tip" src="more/tip.png">
                                <span class="tooltiptextx">Logujesz się do czatu o podanej nazwie, jeżeli on nie istnieje to jest automatycznie tworzony nowy z podanym hasłem. Aby wejść na czat główny, zostaw to pole puste</span>
                            </div>
                            <input class="logininput tipbox" name="chat" placeholder="Chat name" type="text" maxlength="<?php echo $chat_len;?>" >
                            <input class="logininput mg-b-20" name="chatpassw" placeholder="Chat password" type="password" maxlength="<?php echo $chat_passw_len;?>">
                            <p class="mg-b-20 error"><?php echo $chatErr ?></p>
                            <button class="btn mg-b-10" name="submitek" type="submit">Log in</button>
                        </form>
                        <form action="register.php" method="POST">
                            <a class="vanilla mg-t-10"><button class="btn">Register</button></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
