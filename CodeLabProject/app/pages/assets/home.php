<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');
    exit();
}

echo "Witaj, ".$_SESSION['login']."! <a href='logout.php'>Wyloguj się</a>";
?>