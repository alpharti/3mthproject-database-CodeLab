<?php 
session_start();

$secret = "6LcYasEsAAAAAF3xmkbnwBwoMrY0fOuvyFlWR2Tc";
$publicKey = "6LcYasEsAAAAABsEN7ugnhn_NFL7MLQSkP57aFkJ";






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
    if (strlen($pass1)<8 || strlen($pass1)>20)
    {
      $verified=false;
      $_SESSION['e_password']="Password must be between 8 and 20 characters long.";
    }
    $email=$_POST['email'];
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
    // Check empty FIRST
if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
    $verified = false;
    $_SESSION['e_bot'] = "Please complete the reCAPTCHA.";
} else {
    // Only hit Google's API if the token actually exists
    $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    $response = json_decode($check);

    if ($response->success == false) {
        $verified = false;
       
    }
}
require_once "db.php";
$connect = @new mysqli ($host, $db_user, $db_password, $db_name);





  if ($verified==true)
    {
      echo "confirmed validation"; exit();
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
  <link rel="stylesheet" href=" ../assets/style.css"/>
   <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
       <div class="g-recaptcha" data-sitekey="<?php echo $publicKey; ?>"></div>
        <?php
          if (isset($_SESSION['e_bot']))
        {
            echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
            unset($_SESSION['e_bot']);
        }
        ?>
       <button type="submit" id="submit-btn">Register</button>
      
      
      
      
      
      
      
      
      
      
      
      <!-- <input type="hidden" id="recaptcha-token" name="recaptcha-token" />
        -->
  
  
  
  
  </form>
    </fieldset>



</body>
</html>