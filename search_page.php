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
   <title>Search page</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="premium-search-section">
   
   <span class="search-label">Boutique Search</span>
   
   <div class="search-form-container">
      <form action="" method="get" class="luxury-search-box">
         <input type="text" name="search_box" placeholder="What are you looking for today?" maxlength="100" class="box" required value="<?= isset($_GET['search_box']) ? $_GET['search_box'] : ''; ?>">
         <button type="submit" class="fas fa-search search-btn"></button>
      </form>
   </div>

</section>

<section class="products search-results-target" style="padding-top: 0; min-height:100vh;">

   <?php
      if(isset($_GET['search_box'])){
         $search_box = $_GET['search_box'];
         
         // Pagination Logic
         $limit = 12;
         $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
         $offset = ($page - 1) * $limit;

         $total_products_query = $conn->prepare("SELECT COUNT(*) FROM `products` WHERE name LIKE ?");
         $total_products_query->execute(["%{$search_box}%"]);
         $total_rows = $total_products_query->fetchColumn();
         $total_pages = ceil($total_rows / $limit);

         $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE :search LIMIT :limit OFFSET :offset"); 
         $select_products->bindValue(':search', "%{$search_box}%", PDO::PARAM_STR);
         $select_products->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
         $select_products->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
         $select_products->execute();
         
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
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="" loading="lazy">
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
            
            if($total_pages > 1): ?>
            <div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
               <?php if($page > 1): ?>
                  <a href="search_page.php?search_box=<?= $search_box; ?>&page=<?= $page - 1; ?>" class="option-btn" style="width: auto;">Prev</a>
               <?php endif; ?>

               <?php for($i = 1; $i <= $total_pages; $i++): ?>
                  <a href="search_page.php?search_box=<?= $search_box; ?>&page=<?= $i; ?>" class="<?= ($i == $page) ? 'btn' : 'option-btn'; ?>" style="width: auto; padding: 1rem 2rem; min-width: 5rem;"><?= $i; ?></a>
               <?php endfor; ?>

               <?php if($page < $total_pages): ?>
                  <a href="search_page.php?search_box=<?= $search_box; ?>&page=<?= $page + 1; ?>" class="option-btn" style="width: auto;">Next</a>
               <?php endif; ?>
            </div>
            <?php endif;
            
         }else{
            echo '
            <div class="empty-results">
               <i class="fas fa-search-minus"></i>
               <p>We couldn\'t find any pieces matching your request.</p>
               <a href="shop.php" class="btn" style="margin-top: 2rem; display: inline-block; width: auto; padding: 1.5rem 4rem;">Return to Shop</a>
            </div>';
         }
      }
   ?>

</section>












<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>