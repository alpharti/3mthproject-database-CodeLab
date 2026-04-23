<?php
require_once __DIR__ . "/../assets/db.php";
session_start();
if ((!isset($_POST['Login'])) || (!isset($_POST['Password']))) {
    header('Location: index.php');
    exit();
}

// if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
//     header('Location: home.php');
//     exit();
// }


$connect = @new mysqli($host, $db_user, $db_password, $db_name);

if ($connect->connect_errno != 0) {
    echo "Error: " . $connect->connect_errno;
}


$login = $_POST['Login'];
$password = $_POST['Password'];

$login = htmlentities($login, ENT_QUOTES, "UTF-8");
// No need to hash here for verification

// Remove debug output
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

if ($stmt = $connect->prepare("SELECT id, login, email, password FROM users WHERE login = ?")) {
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $how_many_users = $result->num_rows;
    if ($how_many_users > 0) {
        $line = $result->fetch_assoc();
        if (password_verify($password, $line['password'])) {
            $_SESSION['logged'] = true;
            $_SESSION['id'] = $line['id'];
            $_SESSION['login'] = $line['login'];
            $_SESSION['email'] = $line['email'];
            unset($_SESSION['error']);
            $result->free_result();
            $stmt->close();
            header('Location: home.php');
            exit();
        } else {
            $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
        }
    } else {
        $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
    }
    $stmt->close();
    header('Location: index.php');
} else {
    $_SESSION['error'] = '<span style="color:red">Błąd serwera. Spróbuj ponownie później.</span>';
    header('Location: index.php');
}
$connect->close();