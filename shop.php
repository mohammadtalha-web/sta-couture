<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Discover the Collection - STA Couture Shop</title>
   <meta name="description" content="Browse our exclusive collection of luxury fashion, including elegant Sarees, modern Borkhas, and premium Three-pieces at STA Couture.">
   <meta name="keywords" content="boutique shop, buy saree, luxury fashion, STA Couture products">
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h1 class="heading">Latest Products.</h1>

   <div class="box-container">

   <?php
      // Pagination Logic
      $limit = 12;
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $limit;

      $total_products_query = $conn->prepare("SELECT COUNT(*) FROM `products`");
      $total_products_query->execute();
      $total_rows = $total_products_query->fetchColumn();
      $total_pages = ceil($total_rows / $limit);

      $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC LIMIT :limit OFFSET :offset"); 
      $select_products->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
      $select_products->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
      $select_products->execute();

      if($select_products->rowCount() > 0){
       while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>"><img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="" loading="lazy"></a>
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>৳</span><?= $fetch_product['price']; ?><span>/-</span></div>
         <input type="hidden" name="qty" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

   <?php if($total_pages > 1): ?>
   <div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
      <?php if($page > 1): ?>
         <a href="shop.php?page=<?= $page - 1; ?>" class="option-btn" style="width: auto;">Prev</a>
      <?php endif; ?>

      <?php for($i = 1; $i <= $total_pages; $i++): ?>
         <a href="shop.php?page=<?= $i; ?>" class="<?= ($i == $page) ? 'btn' : 'option-btn'; ?>" style="width: auto; padding: 1rem 2rem; min-width: 5rem;"><?= $i; ?></a>
      <?php endfor; ?>

      <?php if($page < $total_pages): ?>
         <a href="shop.php?page=<?= $page + 1; ?>" class="option-btn" style="width: auto;">Next</a>
      <?php endif; ?>
   </div>
   <?php endif; ?>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>