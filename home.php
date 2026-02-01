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
   <title>STA Couture - Premium Fashion Boutique</title>
   <meta name="description" content="Discover the finest Saree, Borkha, and Three-piece collections at STA Couture. Elevate your elegance with our curated luxury fashion pieces.">
   <meta name="keywords" content="fashion, saree, borkha, boutique, luxury clothing, three piece, STA Couture">
   
   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">

   <div class="swiper home-slider">
   
   <div class="swiper-wrapper">

      <div class="swiper-slide slide">
         <div class="image">
            <a href="category.php?category=three"><img src="images/hero_luxury_three.png" alt="Three Piece Fashion" loading="lazy"></a>
         </div>
         <div class="content">
            <span>Trending Style</span>
            <h3>Latest Three Pieces</h3>
            <a href="category.php?category=three" class="btn">Shop Now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <a href="category.php?category=borkha"><img src="images/hero_luxury_borkha.png" alt="Borkha Fashion" loading="lazy"></a>
         </div>
         <div class="content">
            <span>Modest & Elegant</span>
            <h3>Modern Style Borkha</h3>
            <a href="category.php?category=borkha" class="btn">Shop Now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <a href="category.php?category=saree"><img src="images/hero_luxury_saree.png" alt="Saree Fashion" loading="lazy"></a>
         </div>
         <div class="content">
            <span>Special Collection</span>
            <h3>Beautiful Designer Sarees</h3>
            <a href="category.php?category=saree" class="btn">Shop Now</a>
         </div>
      </div>

   </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

</div>

<section class="category">

   <h1 class="heading">The Collections</h1>

   <div class="swiper category-slider">

   <div class="swiper-wrapper">

   <a href="category.php?category=women" class="swiper-slide slide">
      <img src="images/cat-1.png" alt="">
      <h3>Women's Dress</h3>
   </a>

   <a href="category.php?category=three" class="swiper-slide slide">
      <img src="images/cat-3.png" alt="">
      <h3>Three Pieces</h3>
   </a>

   <a href="category.php?category=saree" class="swiper-slide slide">
      <img src="images/cat-5.png" alt="">
      <h3>Saree</h3>
   </a>

   <a href="category.php?category=borkha" class="swiper-slide slide">
      <img src="images/cat-4.png" alt="">
      <h3>Borkha</h3>
   </a>

   <a href="category.php?category=men" class="swiper-slide slide">
      <img src="images/cat-2.png" alt="">
      <h3>Men's Dress</h3>
   </a>

   <a href="category.php?category=shoes" class="swiper-slide slide">
      <img src="images/cat-6.png" alt="">
      <h3>Shoes</h3>
   </a>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>

<?php
$categories = [
    'three' => 'Three Piece Essentials',
    'saree' => 'Elegant Sarees',
    'borkha' => 'Borkha Collections'
];

foreach($categories as $cat_slug => $cat_name):
?>
<section class="home-products">

   <h1 class="heading"><?= $cat_name; ?></h1>

   <div class="swiper products-slider">

   <div class="swiper-wrapper">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ? ORDER BY id DESC LIMIT 12"); 
     $select_products->execute([$cat_slug]);
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="swiper-slide slide">
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
      echo '<p class="empty">no products in this category yet!</p>';
   }
   ?>

   </div>

   <div class="swiper-pagination"></div>

   </div>

   <div class="discovery-footer">
      <a href="category.php?category=<?= $cat_slug; ?>" class="discovery-link">
         Discover Entire Collection <i class="fas fa-chevron-right"></i>
      </a>
   </div>

</section>
<?php endforeach; ?>









<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:false,
   grabCursor: true,
   spaceBetween: 20,
   speed: 600,
   autoplay: {
      delay: 3500,
      disableOnInteraction: false,
   },
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
    },
});

 var swiper = new Swiper(".category-slider", {
   loop:false,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

var swiper = new Swiper(".products-slider", {
   loop:false,
   grabCursor: true,
   spaceBetween: 15,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
      },
      768: {
        slidesPerView: 3,
      },
      991: {
        slidesPerView: 4,
      },
      1300: {
        slidesPerView: 6, /* 6 items per row on PC */
      },
   },
});

</script>

</body>
</html>