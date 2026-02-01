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

   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);

   $old_pass_input = $_POST['old_pass'];
   $new_pass_input = $_POST['new_pass'];
   $cpass_input = $_POST['cpass'];
   $db_pass = $_POST['prev_pass'];

   if(!empty($old_pass_input)){
      // Verify old password (hybrid check for SHA1 or Bcrypt)
      if (password_verify($old_pass_input, $db_pass) || sha1($old_pass_input) === $db_pass) {
         if($new_pass_input != $cpass_input){
            $message[] = 'confirm password not matched!';
         }elseif(!empty($new_pass_input)){
            $hashed_pass = password_hash($new_pass_input, PASSWORD_DEFAULT);
            $update_admin_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_admin_pass->execute([$hashed_pass, $user_id]);
            $message[] = 'password updated successfully!';
         }else{
            $message[] = 'please enter a new password!';
         }
      }else{
         $message[] = 'old password not matched!';
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

<section class="form-container profile-update-section">

   <?php
      $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
      $select_profile->execute([$user_id]);
      if($select_profile->rowCount() > 0){
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
   ?>

   <form action="" method="post" class="luxury-form">
      <h3 class="luxury-heading">Profile Settings</h3>
      <p class="form-tagline">Refine your boutique identity</p>
      
      <input type="hidden" name="prev_pass" value="<?= $fetch_profile["password"]; ?>">
      
      <div class="input-group">
         <span>Full Name</span>
         <input type="text" name="name" required placeholder="Update your name" maxlength="20" class="box" value="<?= $fetch_profile["name"]; ?>">
      </div>
      
      <div class="input-group">
         <span>Email Address</span>
         <input type="email" name="email" required placeholder="Update your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["email"]; ?>">
      </div>

      <div class="password-suite">
         <p class="suite-title">Update Password</p>
         <div class="input-group">
            <input type="password" name="old_pass" placeholder="Enter Current Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
         </div>
         <div class="input-group">
            <input type="password" name="new_pass" placeholder="New Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="new-password">
         </div>
         <div class="input-group">
            <input type="password" name="cpass" placeholder="Confirm New Password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="new-password">
         </div>
         <div class="maison-reveal-toggle" id="maison-reveal-btn">
            <i class="fas fa-eye"></i>
            <span>Reveal Security</span>
         </div>
      </div>

      <input type="submit" value="COMMIT CHANGES" class="btn premium-btn" name="submit">
   </form>

   <?php
      }else{
         echo '<p class="empty">Please login to access profile settings.</p>';
      }
   ?>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>