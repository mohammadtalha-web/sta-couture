<?php

include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass_input = $_POST['pass'];

   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
   $select_admin->execute([$name]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);

   if($select_admin->rowCount() > 0){
      $db_pass = $row['password'];
      
      if (password_verify($pass_input, $db_pass) || sha1($pass_input) === $db_pass) {
         
         if (sha1($pass_input) === $db_pass) {
            $new_hash = password_hash($pass_input, PASSWORD_DEFAULT);
            $update_pass = $conn->prepare("UPDATE `admins` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_hash, $row['id']]);
         }
         
         session_regenerate_id(true);
         $_SESSION['admin_id'] = $row['id'];
         header('location:dashboard.php');
      } else {
         $message[] = 'incorrect username or password!';
      }
   }else{
      $message[] = 'incorrect username or password!';
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

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<section class="form-container">

   <form action="" method="post">
      <h3>Login now</h3>
      <p>Default username = <span>admin</span> & password = <span>111</span></p>
      <input type="text" name="name" required placeholder="enter your username" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="enter your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" class="btn" name="submit">
   </form>

</section>
   
</body>
</html>