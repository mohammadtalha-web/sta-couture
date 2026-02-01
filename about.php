<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
   $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $select_profile->execute([$user_id]);
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
}else{
   $user_id = '';
}

if(isset($_POST['submit_review'])){

   if($user_id == ''){
      $message[] = 'please login first!';
   }else{
      $name = $_POST['name'];
      $name = htmlspecialchars($name, ENT_QUOTES);
      $comment = $_POST['comment'];
      $comment = htmlspecialchars($comment, ENT_QUOTES);
      $rating = $_POST['rating'];
      $rating = htmlspecialchars($rating, ENT_QUOTES);

      try {
         $select_review = $conn->prepare("SELECT * FROM `reviews` WHERE user_id = ?");
         $select_review->execute([$user_id]);

         if($select_review->rowCount() > 0){
            $message[] = 'you have already submitted a review!';
         }else{
            $insert_review = $conn->prepare("INSERT INTO `reviews`(user_id, name, comment, rating) VALUES(?,?,?,?)");
            if($insert_review->execute([$user_id, $name, $comment, $rating])){
               $message[] = 'review submitted successfully!';
            }else{
               $message[] = 'something went wrong, review not submitted!';
            }
         }
      } catch (PDOException $e) {
         $message[] = 'Error: ' . $e->getMessage();
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>STA Fashion - About Us</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="boutique-about">

   <div class="row">

      <div class="content" style="flex: 1 1 100%; text-align: center; max-width: 1000px; margin: 0 auto;">
         <h3 class="luxury-heading">Our Maison Heritage</h3>
         <p class="boutique-tagline">Crafting Elegance Since Inception</p>
         <p style="margin: 2rem auto; max-width: 800px; font-size: 1.6rem; color: var(--light-color); line-height: 2;">Welcome to STA COUTURE, where high-end fashion meets meticulous craftsmanship. We are dedicated to providing a prestigious shopping experience, curated for those who seek high-quality pieces and unparalleled service. Our mission is to manifest excellence in every stitch.</p>

         <p style="margin: 2rem auto; max-width: 800px; font-size: 1.6rem; color: var(--light-color); line-height: 2;">We extend our deepest gratitude to our visionary mentors for their guidance in manifesting this digital boutique, where tradition meets modern innovation. </p>
         <a href="contact.php" class="btn premium-btn" style="margin-top: 3rem;">INQUIRE WITH US</a>
      </div>

   </div>

</section>

<section class="boutique-reviews">
   
   <h2 class="luxury-heading text-center">Customer Testimonials</h2>
   <p class="form-tagline text-center">Voices of our Prestigious Clientele</p>

   <div class="swiper reviews-slider">

   <div class="swiper-wrapper">

   <?php
      $select_reviews = $conn->prepare("SELECT * FROM `reviews` ORDER BY created_at DESC LIMIT 10");
      $select_reviews->execute();
      if($select_reviews->rowCount() > 0){
         while($fetch_reviews = $select_reviews->fetch(PDO::FETCH_ASSOC)){
   ?>
      <div class="swiper-slide slide luxury-review-card">
         <div class="review-quote"><i class="fas fa-quote-left"></i></div>
         <p class="comment"><?= $fetch_reviews['comment']; ?></p>
         <div class="reviewer-meta">
            <div class="stars">
               <?php
                  for($i=1; $i<=5; $i++){
                     if($i <= $fetch_reviews['rating']){
                        echo '<i class="fas fa-star accent-star"></i>';
                     }else{
                        echo '<i class="far fa-star accent-star"></i>';
                     }
                  }
               ?>
            </div>
            <h3 class="reviewer-name"><?= $fetch_reviews['name']; ?></h3>
         </div>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no reviews added yet!</p>';
      }
   ?>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>

<section class="maison-review-suite">

   <div class="luxury-form-container">
      <form action="" method="post" class="luxury-form">
         <h3 class="luxury-heading">Submit Your Legacy</h3>
         <p class="form-tagline">Share your boutique experience</p>

         <?php if($user_id != ''){ ?>
         
         <div class="input-group">
            <span>Identity</span>
            <input type="text" name="name" required placeholder="Display Name" maxlength="100" class="box" value="<?= $fetch_profile['name'] ?? ''; ?>">
         </div>

         <div class="input-group">
            <span>Your Chronicle</span>
            <textarea name="comment" class="box" placeholder="Write your prestigious review..." cols="30" rows="5" required maxlength="500"></textarea>
         </div>

         <div class="input-group">
            <span>Excellence Rating</span>
            <select name="rating" class="box" required>
               <option value="" disabled selected>Select Prestige Level --</option>
               <option value="5">5 Stars - Perfection</option>
               <option value="4">4 Stars - Excellence</option>
               <option value="3">3 Stars - Distinguished</option>
               <option value="2">2 Stars - Satisfactory</option>
               <option value="1">1 Star - Requires Refinement</option>
            </select>
         </div>

         <input type="submit" value="COMMIT REVIEW" class="btn premium-btn" name="submit_review">
         
         <?php }else{ ?>
         <div class="login-prompt">
            <p>Please sign in to share your boutique chronicle.</p>
            <a href="user_login.php" class="btn premium-btn" style="width: auto; padding: 1.5rem 4rem;">Sign In</a>
         </div>
         <?php } ?>
      </form>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:false,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
        slidesPerView:1,
      },
      768: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>