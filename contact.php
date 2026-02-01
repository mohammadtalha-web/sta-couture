<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['send'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if($select_message->rowCount() > 0){
      $message[] = 'already sent message!';
   }else{

      $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);

      $message[] = 'sent message successfully!';

   }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>STA Fashion - Contact Us</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="contact-container">

   <h1 class="heading">Connect With Us</h1>

   <div class="contact-flex-row">

      <div class="contact-info">
         <h3 class="section-title">Our Boutique</h3>
         <p class="description">Visit us for a private consultation or reach out through our dedicated concierge lines.</p>
         
         <div class="info-item">
            <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
            <div class="text-group">
               <span>Location</span>
               <p>STA Fashion House, Banasree Main Road, Block-E, Dhaka</p>
            </div>
         </div>

         <div class="info-item">
            <div class="icon-box"><i class="fas fa-phone"></i></div>
            <div class="text-group">
               <span>Concierge</span>
               <p>+880 1612 684181</p>
            </div>
         </div>

         <div class="info-item">
            <div class="icon-box"><i class="fas fa-envelope"></i></div>
            <div class="text-group">
               <span>Email</span>
               <p>concierge@stafashion.com.bd</p>
            </div>
         </div>

      </div>

      <div class="contact-form-box">
         <form action="" method="post">
            <h3 class="section-title">Send a Query</h3>
            <div class="input-group">
               <input type="text" name="name" placeholder="Full Name" required maxlength="50" class="box">
            </div>
            <div class="input-group">
               <input type="email" name="email" placeholder="Email Address" required maxlength="50" class="box">
            </div>
            <div class="input-group">
               <input type="number" name="number" min="0" max="99999999999" placeholder="Phone Number" required onkeypress="if(this.value.length == 11) return false;" class="box">
            </div>
            <div class="input-group">
               <textarea name="msg" class="box" placeholder="What can we help you with?" cols="30" rows="6" required></textarea>
            </div>
            <input type="submit" value="SEND MESSAGE" name="send" class="btn premium-btn">
         </form>
      </div>

   </div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>