<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   
   if($select_user->rowCount() > 0){
      $message[] = 'email already exists!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $hashed_pass]);
         $message[] = 'registered successfully, login now please!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container register-section">

   <form action="" method="post" class="luxury-form">
      <h3 class="luxury-heading">Join the Maison</h3>
      <p class="form-tagline">Begin your curated journey</p>
      
      <div class="input-group">
         <span>Your Name</span>
         <input type="text" name="name" required placeholder="Enter your full name" maxlength="20" class="box">
      </div>
      
      <div class="input-group">
         <span>Email Identity</span>
         <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>

      <div class="password-suite">
         <p class="suite-title">Security Suite</p>
         <div class="input-group">
            <input type="password" name="pass" required placeholder="Create Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         </div>
         <div class="input-group">
            <input type="password" name="cpass" required placeholder="Confirm Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         </div>
         <div class="maison-reveal-toggle" id="maison-reveal-btn">
            <i class="fas fa-eye"></i>
            <span>Reveal Security</span>
         </div>
      </div>

      <input type="submit" value="CREATE ACCOUNT" class="btn premium-btn" name="submit">
      
      <div class="form-footer">
         <p>Already a member?</p>
         <a href="user_login.php" class="login-link">Sign in to your account</a>
      </div>
   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>