<?php 
require_once __DIR__ . "/../assets/db.php";

mysqli_report(MYSQLI_REPORT_STRICT);

session_start();






if (isset($_POST['email']))
{
  $verified=true;
  $pass1=$_POST['password'];
  $pass2=$_POST['confirm'];
  $nick=$_POST['nick'];
  if (strlen($nick)<3 || strlen($nick)>20)
  {
    $verified=false;
    $_SESSION['e_nick']="Nick must be between 3 and 20 characters long.";
  }
  if (ctype_alnum($nick)==false){
    $verified=false;
    $_SESSION['e_nick']="Nick can only contain letters and numbers.";
  }
    if ($pass1!=$pass2)
    {
      $verified=false;
      $_SESSION['e_password']="Passwords do not match.";
    }
     else if (strlen($pass1)<8 || strlen($pass1)>20)
    {
      $verified=false;
      $_SESSION['e_password']="Password must be between 8 and 20 characters long.";
    }
    $email = $_POST['email'];
    $emailB=filter_var($email, FILTER_SANITIZE_EMAIL);
    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
    {
      $verified=false;
      $_SESSION['e_email']="Please provide a valid email address.";
    }
    $pass_hash=password_hash($pass1, PASSWORD_DEFAULT);

    if (!isset($_POST['terms']))
    {
      $verified=false;
      $_SESSION['e_terms']="You must accept the terms and conditions.";
    }


try 
{
  $connect = new mysqli ($host, $db_user, $db_password, $db_name);
  if ($connect->connect_errno!=0)
  {
    throw new Exception(mysqli_connect_errno());
  }
  else {
    $email_escaped = $connect->real_escape_string($email);
    $stmt = $connect->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $how_many_emails = $result->num_rows;
    $stmt->close();
    if ($how_many_emails>0)
    {
      $verified=false;
      $_SESSION['e_email']="An account with this email already exists.";
    }
    if ($verified)
    {
    $stmt = $connect->prepare("SELECT id FROM users WHERE login=?");
    $stmt->bind_param("s", $nick);
    $stmt->execute();
    $result = $stmt->get_result();
    $how_many_logins = $result->num_rows;
    $stmt->close();
    if ($how_many_logins>0)
    {
      $verified=false;
      $_SESSION['e_nick']="An account with this nickname already exists.";
    }

    if ($verified) {
        $stmt = $connect->prepare("INSERT INTO users (email, login, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $nick, $pass_hash);
        $stmt->execute();
        $stmt->close();
    }
 

  } // closes if ($verified==true) — the second one
  $connect->close();
} // closes else
} // closes try
 
catch (Exception $e)
{
  echo '<span class="error">Server error. Please try again later.</span>';
  //echo "Debug info: ".$e; //uncomment for debugging
}
if ($verified)
    {
echo "Registration successful!"; exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="app/assets/style.css"/>
</head>
<body>
    








<fieldset>
    <Legend>Register an Account</Legend>
    <form id="register-form" method="POST">
      <input id="nick" type="text" name="nick" placeholder="nick" required />
      <?php
         if (isset($_SESSION['e_nick']))
       {
          echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
           unset($_SESSION['e_nick']);
       }


      ?>
      <input id="email" type="email" name="email" placeholder="Email" required />
      <?php
         if (isset($_SESSION['e_email']))
       {
          echo '<div class="error">'.$_SESSION['e_email'].'</div>';
           unset($_SESSION['e_email']);
       }
       ?>
      <input id="password" type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm" placeholder="Confirm Password" required />
      <?php
         if (isset($_SESSION['e_password']))
       {
          echo '<div class="error">'.$_SESSION['e_password'].'</div>';
           unset($_SESSION['e_password']);
       }
      ?>
      <label>
        <input type="checkbox" name="terms" required />
        I agree to the <a href="#">Terms and Conditions</a>
      </label>
      <?php
         if (isset($_SESSION['e_terms']))
       {
          echo '<div class="error">'.$_SESSION['e_terms'].'</div>';
           unset($_SESSION['e_terms']);
       }
       ?>
       <button type="submit" id="submit-btn">Register</button>
      


  
  
  
  </form>
    </fieldset>



</body>
</html>