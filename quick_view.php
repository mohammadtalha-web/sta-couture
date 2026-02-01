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
   <title>Quick view</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="heading">Quick view</h1>

   <?php
     $pid = $_GET['pid'];
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); 
     $select_products->execute([$pid]);
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <div class="row">
         <div class="image-container">
            <div class="main-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
            </div>
            <div class="sub-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="">
            </div>
         </div>
         <div class="content">
            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="price">Tk <?= $fetch_product['price']; ?></div>
            
            <div class="quantity-control">
               <label>Quantity</label>
               <div class="qty-selector">
                  <button type="button" class="qty-btn minus"><i class="fas fa-minus"></i></button>
                  <input type="number" name="qty" class="qty" min="1" max="99" value="1" readonly>
                  <button type="button" class="qty-btn plus"><i class="fas fa-plus"></i></button>
               </div>
            </div>

            <div class="details-list">
               <div class="detail-item">
                  <span class="label">Product Code</span>
                  <span class="value"><?= $fetch_product['p_code']; ?></span>
               </div>
               <div class="detail-item clickable">
                  <span class="label">Product Description</span>
                  <i class="fas fa-chevron-right"></i>
               </div>
               <div class="detail-content"><?= $fetch_product['details']; ?></div>
            </div>

            <div class="action-buttons">
               <input type="submit" value="ADD TO CART" class="add-btn" name="add_to_cart">
               <button type="submit" name="add_to_wishlist" class="icon-btn wishlist-btn">
                  <i class="far fa-heart"></i>
               </button>
            </div>
         </div>
      </div>
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

</section>













<?php include 'components/footer.php'; ?>

<script>
document.querySelectorAll('.qty-btn').forEach(button => {
   button.onclick = (e) => {
      const input = button.parentElement.querySelector('.qty');
      let val = parseInt(input.value);
      if (button.classList.contains('plus')) {
         if (val < 99) input.value = val + 1;
      } else {
         if (val > 1) input.value = val - 1;
      }
   };
});

document.querySelectorAll('.quick-view .detail-item.clickable').forEach(item => {
   item.onclick = () => {
      let content = item.nextElementSibling;
      if (content && content.classList.contains('detail-content')) {
         let isVisible = window.getComputedStyle(content).display !== 'none';
         content.style.display = isVisible ? 'none' : 'block';
         item.querySelector('i').style.transform = isVisible ? 'rotate(0deg)' : 'rotate(90deg)';
      }
   };
});

document.querySelectorAll('.quick-view .image-container .sub-image img').forEach(image => {
   image.onclick = () => {
      src = image.getAttribute('src');
      document.querySelector('.quick-view .image-container .main-image img').src = src;
   }
});
</script>

<script src="js/script.js"></script>

</body>
</html>