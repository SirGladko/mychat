<?php 
    //db connection and basic variables
    include('more/conf.php');

    //add message to db
    if(isset($_POST["wiadomosc"])){
        $message=htmlspecialchars(addslashes($_POST["wiadomosc"]));
        //check if message isnt too long
        if (strlen($message)<$max_char+1){
            if ($message!=""){
                //filter messages with "/" prefix 
                if ($message[0]=="/"){
                    if ($_SESSION["permiss"]==1){
                        //checking for commands
                        $command = explode(" ", $message);
                        //user ban
                        if ($command[0]=="/ban"){
                            $exist=0;
                            $banneduser=$command[1];
                            $sql="SELECT login, user_flag FROM users;";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()){
                                //if exists and is a common user then ban it
                                if ($banneduser==$row["login"] && $row["user_flag"]==0){
                                    $sql1="UPDATE users SET user_flag=2 where login='$banneduser';";
                                    $result1 = $conn->query($sql1);
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 5, '$banneduser got banned');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                                //if is an admin then no xD
                                else if ($banneduser==$row["login"] && $row["user_flag"]==1){
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'You cant ban an admin');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                            }
                            //if doesnt exist
                            if ($exist==0){
                                $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'That user doesnt exist or is already banned');";
                                $result = $conn->query($sql);
                            }
                        }
                        //unban
                        else if ($command[0]=="/unban"){
                            $exist=0;
                            $unbanned=$command[1];
                            $sql="SELECT login, user_flag FROM users;";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()){
                                if($unbanned==$row["login"] && $row["user_flag"]==2){
                                    $sql1="UPDATE users SET user_flag=0 where login='$unbanned';";
                                    $result1 = $conn->query($sql1);
                                    $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 5, '$unbanned got unbanned');";
                                    $result1 = $conn->query($sql1);
                                    $exist=1;
                                }
                            }
                            if ($exist==0){
                                $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'That user doesnt exist or isnt banned');";
                                $result = $conn->query($sql);
                            }
                        }
                        //delete records
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
                        //permission change
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
                                        $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, '$permissuser got admin rights');";
                                        $result1 = $conn->query($sql1);
                                    }
                                    else if($newpermiss==0 && $row["user_flag"]==1){
                                        $sql1="UPDATE users SET user_flag='0' WHERE login='$permissuser';";
                                        $result1 = $conn->query($sql1);
                                        $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, '$permissuser got common user rights');";
                                        $result1 = $conn->query($sql1);
                                    }
                                }
                            }
                        }
                        //echo deleted record
                        else if($command[0]=="/echo"){
                            $echorecord=$command[1];
                            $sql="SELECT record_flag, text FROM records WHERE record_id='$echorecord'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            if ($row["record_flag"]==6){
                                $deletedrecord=$row["text"];
                                $sql1="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Record no. $echorecord: \"$deletedrecord\"');";
                                $result1 = $conn->query($sql1);
                            }
                        }
                        //if any command wasnt written after "/" then echo command list
                        else {
                        $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 7, 'wyświetlono komendy');";
                            $result = $conn->query($sql);
                        }
                    }
                    //if has no admin rights
                    else{
                        $sql="INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id','$chat_id', 4, 'Nie posiadasz uprawnień administratora, aby używać komend');";
                        $result = $conn->query($sql);
                    }
                }
                //add record to db
                else{
                    $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 0, '$message');";
                    $result = $conn->query($sql);
                }
            }
        }
        else{
            $sql = "INSERT INTO records (user_id, chat_id, record_flag, text) values ('$user_id', '$chat_id', 4, 'Message is too long');";
            $result = $conn->query($sql);
        }
    }
?>
