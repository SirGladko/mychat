<?php 
    //podłączenie do bazy danych i podstawowe zmienne
    include('more/conf.php');

    $loginErr="";
    $chatErr="";
    //defaultowy czat
    if (isset($_POST["submitek"])){
        //sprawdzanie wpisanego czatu
        if (isset($_POST["chat"])){
            //czy nie jest za długi
            if(strlen($_POST["chat"])>$chat_len){
                $chatErr="Wprowadzony czat jest za długi";
            }
            else{
                //defaultowy czat
                if (strlen($_POST["chat"])==0){
                    $chat="default";
                    $chatpassw="";
                }
                //czat wprowadzony
                else{
                    $chat=htmlspecialchars(addslashes($_POST["chat"]));
                    $chatpassw=addslashes($_POST["chatpassw"]);
                }
                //sprawdzanie czy wprowadzony czat istnieje
                $sql = "SELECT chat_id, chat_name, chat_password from chats where chat_name='$chat';";
                $result = $conn->query($sql);
                if ($result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    //sprawdzanie czy wprowadzone hasło nie jest za długie
                    if(strlen($chatpassw)>$chat_passw_len){
                        $chatErr="Wprowadzone hasło jest za długie";
                    }
                    //sprawdzenie poprawności hasła
                    else{
                        if ($row["chat_password"]==$chatpassw){
                            $chat=$row["chat_name"];
                            $chat_id=$row["chat_id"];
                        }
                        else{
                            $chatErr="Złe hasło do czatu";
                        }
                    }
                }
                //tworzenie nowego czatu, jeżeli taki nie istnieje
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
        //sprawdzanie loginu i hasla
        if (strlen($login)==0 or strlen($login)>$login_len){
            $loginErr="zły login lub hasło";
        }
        else{
            $sql = "SELECT user_id, login, password, user_flag from users where login='$login';";
            $result = $conn->query($sql);
            if ($result->num_rows == 1){
                $row = $result->fetch_assoc();
                //jeżeli hasło poprawne
                if ($row["login"]==$login && $row["password"]==$password){
                    //jeżeli konto jest z flagą bana
                    if ($row["user_flag"]==2){
                        $loginErr="Konto zostało zablokowane";
                    }
                    else{
                        //jeżeli dobrze wpisany chat
                        if ($chatErr==""){
                            $_SESSION["login"]=$row["login"];
                            $_SESSION["user_id"]=$row["user_id"];
                            $_SESSION["chat"]=$chat;
                            $_SESSION["chat_id"]=$chat_id;

                            $user_id = $row["user_id"];
                            //update ostatniej daty logowania oraz do którego czatu jest się zalogowanym
                            $sql = "UPDATE users SET date = current_timestamp, last_active = current_timestamp, chat_used = '$chat_id' WHERE user_id = '$user_id';";
                            $result = $conn->query($sql);
                            //info o dołączeniu do czatu
                            $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 2, '***$login IN***');";
                            $result = $conn->query($sql);
                        }
                    }
                }
                else{
                    $loginErr="zły login lub hasło";
                }
            }
            else{
                $loginErr="zły login lub hasło";
            }
        }
    }

    //jeżeli sesja użytkownika nie jest pusta to przejdź do czatu
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
            //jeżeli został wylogowany przez bana
            if (isset($_SESSION["ban"])){
                if ($_SESSION["ban"]==1){
                    echo "<script>alert(\"Twoje konto zostało zablokowane\");</script>";
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
                            <input class="logininput" name="passw" placeholder="Hasło" type="password" maxlength="<?php echo $passw_len;?>" required>
                            <div class="mg-b-20 error"><?php echo $loginErr ?></div>
                            <div class="tooltipx">
                                <img class="tip" src="more/tip.png">
                                <span class="tooltiptextx">Logujesz się do czatu o podanej nazwie, jeżeli on nie istnieje to jest automatycznie tworzony nowy z podanym hasłem. Aby wejść na czat główny, zostaw to pole puste</span>
                            </div>
                            <input class="logininput tipbox" name="chat" placeholder="Nazwa czatu" type="text" maxlength="<?php echo $chat_len;?>" >
                            <input class="logininput mg-b-20" name="chatpassw" placeholder="Hasło czatu" type="password" maxlength="<?php echo $chat_passw_len;?>">
                            <p class="mg-b-20 error"><?php echo $chatErr ?></p>
                            <button class="btn mg-b-10" name="submitek" type="submit">Zaloguj się</button>
                        </form>
                        <form action="register.php" method="POST">
                            <a class="vanilla mg-t-10"><button class="btn">Zarejestruj się</button></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>