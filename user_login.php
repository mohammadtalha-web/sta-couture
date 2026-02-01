<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass_input = $_POST['pass'];

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $db_pass = $row['password'];
      
      // Hybrid Check: Try Bcrypt first, then SHA1 (legacy)
      if (password_verify($pass_input, $db_pass) || sha1($pass_input) === $db_pass) {
         
         // If it was SHA1, upgrade it to Bcrypt now
         if (sha1($pass_input) === $db_pass) {
            $new_hash = password_hash($pass_input, PASSWORD_DEFAULT);
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_hash, $row['id']]);
         }
         
         session_regenerate_id(true);
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      } else {
         $message[] = 'incorrect email or password!';
      }
   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container login-section">

   <form action="" method="post" class="luxury-form">
      <h3 class="luxury-heading">Welcome Back</h3>
      <p class="form-tagline">Re-enter the world of sta couture</p>
      
      <div class="input-group">
         <span>Email Identity</span>
         <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      </div>

      <div class="password-suite">
         <p class="suite-title">Security Gateway</p>
         <div class="input-group">
            <input type="password" name="pass" required placeholder="Enter Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="current-password">
         </div>
         <div class="maison-reveal-toggle" id="maison-reveal-btn">
            <i class="fas fa-eye"></i>
            <span>Reveal Gateway</span>
         </div>
      </div>

      <input type="submit" value="SIGN IN" class="btn premium-btn" name="submit">
      
      <div class="form-footer">
         <p>New to the Maison?</p>
         <a href="user_register.php" class="login-link">Discover your identity</a>
      </div>
   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>