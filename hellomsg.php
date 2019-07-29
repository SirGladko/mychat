<?php 
    //podłączenie do bazy danych i podstawowe zmienne
    include('more/conf.php');

    //ponowne pobieranie flagi aktualnego użytkownika przydatne przy odświeżaniu koloru na czacie w razie zmiany uprawnień
    $sql = "SELECT user_flag from users where user_id='$user_id';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    //uprawnienia
    $_SESSION["permiss"] = $row["user_flag"];
    //kolorek na czacie
    if ($_SESSION["permiss"]==1){
        $user="\"admin\"";
    }
    else {
        $user="\"user\"";
    }
    echo "<p>Witaj <p class=".$user.">".$login."</p> na czacie "."<p class=\"user\">".$_SESSION["chat"]."</p></p>";
?>