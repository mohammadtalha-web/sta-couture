<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

include 'components/wishlist_cart.php';

if(isset($_GET['search_box'])){
   $search_box = $_GET['search_box'];
   $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);

   if(!empty($search_box)){
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?"); 
      $select_products->execute(["%{$search_box}%"]);
      
      if($select_products->rowCount() > 0){
         echo '<h2 class="results-heading">Results for <span>"'.$search_box.'"</span></h2>';
         echo '<div class="box-container">';
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
            ?>
            <form action="" method="post" class="box">
               <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
               <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
               <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
               <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
               <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
               <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
               <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>"><img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt=""></a>
               <div class="name"><?= $fetch_product['name']; ?></div>
               <div class="flex">
                  <div class="price"><span>৳</span><?= $fetch_product['price']; ?><span>/-</span></div>
                  <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
               </div>
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            </form>
            <?php
         }
         echo '</div>';
      }else{
         echo '
         <div class="empty-results">
            <i class="fas fa-search-minus"></i>
            <p>We couldn\'t find any pieces matching your request.</p>
            <a href="shop.php" class="btn" style="margin-top: 2rem; display: inline-block; width: auto; padding: 1.5rem 4rem;">Return to Shop</a>
         </div>';
      }
   }
}

?>
