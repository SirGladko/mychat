<?php 
    //podłączenie do bazy danych i podstawowe zmienne
    include('more/conf.php');

    //dodawanie wiadomosci do bazy danych
    if(isset($_POST["wiadomosc"])){
        $message=htmlspecialchars(addslashes($_POST["wiadomosc"]));
        if (strlen($message)<$max_char+1){
            if ($message!=""){
                //filtrowanie wiadomości z prefixem "/" używanym do komend
                if ($message[0]=="/"){
                    if ($_SESSION["permiss"]==1){
                        //sprawdzanie poszczególnych komend
                        $command = explode(" ", $message);
                        //banowanie użytkowników
                        if ($command[0]=="/ban"){
                            $exist=0;
                            $banneduser=$command[1];
                            $sql="SELECT login, user_flag FROM users;";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()){
                                //jeżeli użytkownik istnieje i ma flage zwykłego użytkownika to zablokuj go
                                if ($banneduser==$row["login"] && $row["user_flag"]==0){
                                    $sql1="UPDATE users SET user_flag=2 where login='$banneduser';";
                                    $result1 = $conn->query($sql1);
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 5, 'Zablokowano użytkownika $banneduser');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                                //jeżeli ma flage administratora to nie xD
                                else if ($banneduser==$row["login"] && $row["user_flag"]==1){
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Nie można zablokować użytkownika z prawami administratora');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                            }
                            //jeżeli nie ma takiego użytkownika
                            if ($exist==0){
                                $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Podany użytkownik nie istnieje lub jest już zablokowany');";
                                $result = $conn->query($sql);
                            }
                        }
                        //odbanowanie
                        else if ($command[0]=="/unban"){
                            $exist=0;
                            $unbanned=$command[1];
                            $sql="SELECT login, user_flag FROM users;";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()){
                                if($unbanned==$row["login"] && $row["user_flag"]==2){
                                    $sql1="UPDATE users SET user_flag=0 where login='$unbanned';";
                                    $result1 = $conn->query($sql1);
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 5, 'Odbanowano użytkownika $unbanned');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                            }
                            if ($exist==0){
                                $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Podany użytkownik nie istnieje lub nie jest zablokowany');";
                                $result = $conn->query($sql);
                            }
                        }
                        //usuwanie wpisów
                        else if($command[0]=="/del"){
                            $delrecord=$command[1];
                            $sql="SELECT record_flag FROM records WHERE record_id='$delrecord'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            if ($row["record_flag"]==0){
                                $sql="UPDATE records SET record_flag='6' where record_id='$delrecord'";
                                $result = $conn->query($sql);
                            }
                        }
                        //zmiana permisji użytkownika
                        else if($command[0]=="/permiss"){
                            $permissuser=$command[1];
                            $newpermiss=$command[2];
                            $sql="SELECT login, user_flag FROM users";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()){
                                if ($permissuser==$row["login"]){
                                    if($newpermiss==1 && $row["user_flag"]==0){
                                        $sql1="UPDATE users SET user_flag='1' WHERE login='$permissuser';";
                                        $result1 = $conn->query($sql1);
                                        $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Nadano użytkownikowi $permissuser prawa administratora');";
                                        $result1 = $conn->query($sql1);
                                    }
                                    else if($newpermiss==0 && $row["user_flag"]==1){
                                        $sql1="UPDATE users SET user_flag='0' WHERE login='$permissuser';";
                                        $result1 = $conn->query($sql1);
                                        $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Nadano użytkownikowi $permissuser prawa zwykłego użytkownika');";
                                        $result1 = $conn->query($sql1);
                                    }
                                }
                            }
                        }
                        //wypisywanie usuniętych wpisów
                        else if($command[0]=="/echo"){
                            $echorecord=$command[1];
                            $sql="SELECT record_flag, text FROM records WHERE record_id='$echorecord'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            if ($row["record_flag"]==6){
                                $deletedrecord=$row["text"];
                                $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Treść wpisu nr. $echorecord: \"$deletedrecord\"');";
                                $result1 = $conn->query($sql1);
                            }
                        }
                        //jeżeli została wpisana zła komenda to wypisz liste komend
                        else {
                        $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 7, 'wyświetlono komendy');";
                            $result = $conn->query($sql);
                        }
                    }
                    //jeżeli nie posiada się uprawnień administratora
                    else{
                        $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Nie posiadasz uprawnień administratora, aby używać komend');";
                        $result = $conn->query($sql);
                    }
                }
                //dodanie zwykłego wpisu do bazy danych
                else{
                    $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 0, '$message');";
                    $result = $conn->query($sql);
                }
            }
        }
        else{
            $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 4, 'Wiadomość jest za długa');";
            $result = $conn->query($sql);
        }
    }
?>