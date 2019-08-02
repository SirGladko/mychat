<?php 
    //db connection and basic variables
    include('more/conf.php');
    $registerErr="";
    $allowed=array("q","w","e","r","t","y","u","i","o","p","a","s","d","f","g","h","j","k","l","z","x","c","v","b","n","m","1","2","3","4","5","6","7","8","9","0","ą","ę","ś","ż","ź","ć","ń","ł","ó");
    
    if(isset($_POST["register"])){
        $login=$_POST["newlogin"];
        $password=addslashes($_POST["newpassw"]);
        $checklogin=str_split(mb_strtolower($login, 'UTF-8'));
        //check login lenght
        if (strlen($login)==0){
            $registerErr = "Login nie może być pusty";
        }
        else if (strlen($login)<$login_len+1){
            //check if every char is allowed
            foreach($checklogin as $character){
                if ($character!=""){
                    if (!in_array($character, $allowed)) {
                        $registerErr = "Only polish chars and digits are allowed";
                    }
                }
            }
        }
        else {
            $registerErr = "Login is too long";
        }
        if (strlen($password)>$passw_len){
            if($registerErr==""){
                $registerErr = "Password is too long";
            }
            else{
                $registerErr = "Login and password are too long";
            }
        }
        if($registerErr==""){
            $sql = "SELECT user_id, login from users where login='$login';";
            $result = $conn->query($sql);
            //if user doesnt exist then register
            if ($result->num_rows == 0){
                $sql = "INSERT INTO users (login, password, user_flag) values ('$login', '$password', 0);";
                $result = $conn->query($sql);
                header("location: login.php");
            }
            //if user exist
            else {
                $registerErr="Podany login jest już zajęty";
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>register</title>
        <link rel="stylesheet" type="text/css" href="more/style.css">
        <link rel="stylesheet" href="more/bootstrap.css">
        <link rel="stylesheet" href="more/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
        </script>
    </head>
    <body>
        <div class="container">
            <div class="col-lg-12 centered">
                <div class="loginbox">
                    <div class="login">
                        <form action="" method="POST">
                            <input class="logininput" id="input" name="newlogin" placeholder="Login" type="text" maxlength="<?php echo $login_len;?>" required autofocus>
                            <input class="logininput" id="input" name="newpassw" placeholder="Hasło" type="password" maxlength="<?php echo $passw_len;?>" required>
                            <p class="error mg-b-20"><?php echo $registerErr ?></p>
                            <button class="btn" type="submit" name="register">Register</button>
                        </form>
                        <form action="login.php" method="POST">
                            <a class="vanilla mg-t-10"><button class="btn">Back to login</button></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
