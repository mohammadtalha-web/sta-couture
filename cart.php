<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>STA Fashion - Shopping Cart</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="shopping-cart">

   <h1 class="heading">Shopping Cart</h1>

   <div class="cart-main-container">

      <div class="cart-items-container">
         <?php
            $grand_total = 0;
            try {
               $select_cart = $conn->prepare("SELECT c.*, p.p_code FROM `cart` c LEFT JOIN `products` p ON c.pid = p.id WHERE c.user_id = ?");
               $select_cart->execute([$user_id]);
               if($select_cart->rowCount() > 0){
                  while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
         ?>
         <form action="" method="post" class="cart-item">
            <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
            <div class="image-box">
               <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>"><img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt=""></a>
            </div>
            <div class="info-box">
               <div class="name"><?= $fetch_cart['name']; ?></div>
               <div class="code">Code: <span><?= $fetch_cart['p_code'] ? $fetch_cart['p_code'] : 'N/A'; ?></span></div>
               <div class="price">৳<?= $fetch_cart['price']; ?>/-</div>
               
               <div class="controls-row">
                  <div class="qty-selector">
                     <button type="submit" name="update_qty" class="qty-btn minus"><i class="fas fa-minus"></i></button>
                     <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" readonly>
                     <button type="submit" name="update_qty" class="qty-btn plus"><i class="fas fa-plus"></i></button>
                  </div>
                  <button type="submit" class="fas fa-trash-alt delete-icon" name="delete" onclick="return confirm('remove this from cart?');"></button>
               </div>
               
               <div class="sub-total"> Sub Total : <span>৳<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div>
            </div>
         </form>
         <?php
                  $grand_total += $sub_total;
                  }
               }else{
                  echo '<div class="empty-cart-box">
                     <i class="fas fa-shopping-bag"></i>
                     <p>Your sanctuary is currently empty.</p>
                     <a href="shop.php" class="btn">DISCOVER PIECES</a>
                  </div>';
               }
            } catch (Exception $e) {
               echo '<div class="empty-cart-box">
                  <i class="fas fa-sync-alt fa-spin"></i>
                  <p>Synchronizing cart data...</p>
               </div>';
            }
         ?>
      </div>

      <?php if($grand_total > 0): ?>
      <div class="cart-summary">
         <h3 class="summary-heading">Order Summary</h3>
         <div class="summary-details">
            <div class="summary-row">
               <span>Total Items</span>
               <span><?php 
                  $total_items = $conn->prepare("SELECT SUM(quantity) as total FROM `cart` WHERE user_id = ?");
                  $total_items->execute([$user_id]);
                  $total_res = $total_items->fetch(PDO::FETCH_ASSOC);
                  echo $total_res['total'] ? $total_res['total'] : 0;
               ?></span>
            </div>
            <div class="divider"></div>
            <div class="summary-row grand-total">
               <span>Total</span>
               <span>৳<?= $grand_total; ?>/-</span>
            </div>
         </div>
         <div class="action-links">
            <a href="checkout.php" class="btn premium-btn">PROCEED TO CHECKOUT</a>
            <div class="bottom-links">
               <a href="shop.php" class="shop-link">Continue Shopping</a>
               <a href="cart.php?delete_all" class="clear-link" onclick="return confirm('clear all items?');">Clear All</a>
            </div>
         </div>
      </div>
      <?php endif; ?>

   </div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>