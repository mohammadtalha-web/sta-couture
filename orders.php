<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">Placed Orders.</h1>

   <div class="box-container">

      <?php
         if($user_id == ''){
            echo '<div class="empty">
                     <i class="fas fa-lock"></i>
                     <p>Please login to view your official ledger.</p>
                     <a href="user_login.php" class="btn">PORTAL LOGIN</a>
                  </div>';
         }else{
            try {
               $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY id DESC");
               $select_orders->execute([$user_id]);
               if($select_orders->rowCount() > 0){
                  while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="order-box">
         <div class="order-header">
            <div class="header-left">
               <span class="order-id">Order ID: #<?= $fetch_orders['id']; ?></span>
               <h3 class="order-date"><i class="far fa-calendar-alt"></i> <?= $fetch_orders['placed_on']; ?></h3>
            </div>
            <span class="status-badge <?= $fetch_orders['payment_status']; ?>">
               <?= $fetch_orders['payment_status']; ?>
            </span>
         </div>
   
         <div class="info-row">
            <div class="icon-box"><i class="fas fa-user-tie"></i></div>
            <div class="label-group">
               <span class="label">CLIENT</span>
               <span class="value"><?= $fetch_orders['name']; ?></span>
            </div>
         </div>
   
         <div class="info-row">
            <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
            <div class="label-group">
               <span class="label">CONTACT</span>
               <span class="value"><?= $fetch_orders['number']; ?></span>
            </div>
         </div>
   
         <div class="info-row">
            <div class="icon-box"><i class="fas fa-envelope-open-text"></i></div>
            <div class="label-group">
               <span class="label">EMAIL</span>
               <span class="value"><?= $fetch_orders['email']; ?></span>
            </div>
         </div>
   
         <div class="info-row">
            <div class="icon-box"><i class="fas fa-map-marked-alt"></i></div>
            <div class="label-group">
               <span class="label">SHIPPING ARCHITECTURE</span>
               <span class="value"><?= $fetch_orders['address']; ?></span>
            </div>
         </div>
   
         <div class="info-row">
            <div class="icon-box"><i class="fas fa-credit-card"></i></div>
            <div class="label-group">
               <span class="label">PAYMENT CHANNEL</span>
               <span class="value"><?= $fetch_orders['method']; ?></span>
            </div>
         </div>
   
         <div class="order-items">
            <p class="items-text"><?= $fetch_orders['total_products']; ?></p>
         </div>
   
         <div class="order-total">
            <span class="total-label">TOTAL ACQUISITION</span>
            <span class="total-price">৳<?= $fetch_orders['total_price']; ?>/-</span>
         </div>
      </div>
      <?php
                  }
               }else{
                  echo '<div class="empty">
                           <i class="fas fa-history"></i>
                           <p>Your official order ledger is currently empty.</p>
                           <a href="shop.php" class="btn">DISCOVER PIECES</a>
                        </div>';
               }
            } catch (Exception $e) {
               echo '<div class="empty">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Order history is currently synchronizing...</p>
                     </div>';
            }
         }
      ?>

   </div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>