<?php
    //db connection and basic variables
    include('more/conf.php');

    //get user flag and login date
    $sql = "SELECT user_flag, date from users where user_id='$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    //login date
    $_SESSION["logindate"] = $row["date"];
    //user flag
    $_SESSION["permiss"] = $row["user_flag"];

    //logout if login session is empty
    if (empty($_SESSION["login"])){
        header("Location: login.php");
    }
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title><?php echo $chat;?></title>
    <link rel="stylesheet" type="text/css" href="more/style.css">
    <link rel="stylesheet" href="more/bootstrap.css">
    <link rel="stylesheet" href="more/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
    </script>
</head>
<body>
    <div class="container">
        <div class="col-lg-12">
            <div class="col-lg-9">
                <div id="hellomsg">Hello // on chat //</div>
                <div class="chatbox">
                    <div class="chat scroll" id="scroll"> 
                    </div>
                </div>
            </div> 
            <div class="col-lg-3">
                <div class="userlist">
                    <div class="title centered">
                        Lista użytkowników
                    </div>
                    <div id="userlist">
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="col-lg-8"><input class="chatinput" id="wiadomosc" type="text" maxlength="<?php echo $max_char;?>" autofocus autocomplete="off"></div>
                <div class="col-lg-2"><button class="btn send" onclick="submitek()">Wyślij</button></div>
                <div class="col-lg-2"><button class="btn" onclick="logout()">Logout</button></div>
            </div>
        </div>
    </div>
    <script src="more/js.js"></script>
</body>
