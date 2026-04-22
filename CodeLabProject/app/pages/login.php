<?php
require_once __DIR__ . "/db.php";
session_start();
if ((!isset($_POST['Login'])) || (!isset($_POST['Password']))) {
    header('Location: index.php');
    exit();
}
else if ($_SESSION['logged'] == true) {
    header('Location: home.php');
    exit();
}


$connect = @new mysqli ($host, $db_user, $db_password, $db_name);

if ($connect->connect_errno!=0)
    {
        echo "Error: ".$connect->connect_errno;
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
$login = $_POST['Login'];
$password = $_POST['Password'];

$login = htmlentities($login, ENT_QUOTES, "UTF-8");
$password = htmlentities($password, ENT_QUOTES, "UTF-8");


if ($result = @$connect->query(
    sprintf("SELECT * FROM users WHERE login='%s' AND password='%s'", $login, $password
    mysqli_real_escape_string($connect, $login)
    mysqli_real_escape_string($connect, $password))))
    {
        $how_many_users = $result->num_rows;
        if($how_many_users>0)
            {
                    $line = $result->fetch_assoc();
                    if (password_verify($password, $line['password'])) {
                


                $_SESSION['logged'] = true; 
                    
                    $_SESSION['id'] = $line['id'];
                    $_SESSION['login'] = $line['login'];
                    $_SESSION['email'] = $line['email'];

                unset($_SESSION['error']); 

                $result->free_result();
                header('Location: home.php');
            } else {
            $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
            header('Location: index.php');
            } else {
            $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
            header('Location: index.php');
            }
            }
    }
$connect->close();
?>          