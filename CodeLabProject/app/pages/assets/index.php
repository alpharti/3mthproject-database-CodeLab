<?php
session_start();
if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    header('Location: home.php');
    exit();
}
?>





<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLab</title>
</head>
<body>
   <form action="login.php" type="POST">
    <input type="text" name="Login" placeholder="Login">
    <input type="password" name="Password" placeholder="Hasło">
    <button type="submit" name="submit">Zaloguj</button>
   </form>
   <a href="registration.php">Zarejestruj się</a>
<?php
if(isset($_SESSION['error'])) {
    echo $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
</nav></div>
</body>
</html>