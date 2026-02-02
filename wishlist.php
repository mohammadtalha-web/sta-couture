<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

include 'components/wishlist_cart.php';

if(isset($_POST['delete'])){
   $wishlist_id = $_POST['wishlist_id'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$wishlist_id]);
}

if(isset($_GET['delete_all'])){
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
}

if(isset($_POST['add_all_to_cart'])){
   $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
   $select_wishlist->execute([$user_id]);
   
   if($select_wishlist->rowCount() > 0){
      while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
         $pid = $fetch_wishlist['pid'];
         $name = $fetch_wishlist['name'];
         $price = $fetch_wishlist['price'];
         $image = $fetch_wishlist['image'];
         $qty = 1; // Default quantity for bulk add

         $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
         $check_cart_numbers->execute([$name, $user_id]);

         if($check_cart_numbers->rowCount() == 0){
            $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
            $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         }
      }
      
      $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
      $delete_wishlist->execute([$user_id]);
      $message[] = 'all items added to cart!';
   }else{
      $message[] = 'your collection is empty!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Wishlist</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="wishlist-container">

   <h1 class="heading">Your Curated Collection</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      try {
         $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
         $select_wishlist->execute([$user_id]);
         if($select_wishlist->rowCount() > 0){
            while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
               $grand_total += $fetch_wishlist['price'];  
      ?>
      <form action="" method="post" class="wishlist-box">
         <input type="hidden" name="pid" value="<?= $fetch_wishlist['pid']; ?>">
         <input type="hidden" name="wishlist_id" value="<?= $fetch_wishlist['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_wishlist['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_wishlist['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_wishlist['image']; ?>">
         
         <div class="image-frame">
            <a href="quick_view.php?pid=<?= $fetch_wishlist['pid']; ?>">
               <img src="uploaded_img/<?= $fetch_wishlist['image']; ?>" alt="">
            </a>
            <button class="remove-btn" type="submit" name="delete" onclick="return confirm('Remove this piece from your collection?');">
               <i class="fas fa-times"></i>
            </button>
         </div>
   
         <div class="content">
            <span class="curated-label">Curated Piece</span>
            <div class="name"><?= $fetch_wishlist['name']; ?></div>
            
            <div class="details-row">
               <div class="price"><span>৳</span><?= $fetch_wishlist['price']; ?><span>/-</span></div>
               <div class="qty-control">
                  <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
               </div>
            </div>
   
            <input type="submit" value="ADD TO CART" class="btn premium-btn" name="add_to_cart">
         </div>
      </form>
      <?php
            }
         }else{
            $show_empty = true;
         }
      } catch (Exception $e) {
         $show_empty = true; // Fallback to empty state on error
      }
      ?>
   </div>

   <?php
      if(isset($show_empty)){
         echo '<div class="empty-results">
                  <i class="fas fa-heart"></i>
                  <p>Your curated collection is currently empty.</p>
                  <a href="shop.php" class="btn" style="margin-top: 2rem; display: inline-block; width: auto; padding: 1.5rem 4rem;">Explore Boutique</a>
               </div>';
      }
   ?>

   <?php if($grand_total > 0): ?>
   <div class="collection-summary">
      <div class="summary-card">
         <h3 class="summary-title">Collection Summary</h3>
         <div class="total-row">
            <span>Total Value</span>
            <div class="total-price">৳<?= $grand_total; ?><span>/-</span></div>
         </div>
         <div class="action-buttons">
            <form action="" method="post">
               <input type="submit" value="ADD ALL TO CART" name="add_all_to_cart" class="btn premium-btn" style="padding: 1.5rem 4rem;">
            </form>
            <a href="shop.php" class="option-btn">Continue Exploring</a>
            <a href="wishlist.php?delete_all" class="delete-all-btn" onclick="return confirm('Clear your entire curated collection?');">Clear Collection</a>
         </div>
      </div>
   </div>
   <?php endif; ?>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>