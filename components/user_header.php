<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">sta <span>couture</span></a>

      <nav class="navbar">
         <a href="home.php">Home</a>
         <a href="about.php">About Us</a>
         <a href="orders.php">Orders</a>
         <a href="shop.php">Shop Now</a>
         <a href="contact.php">Contact Us</a>
      </nav>

      <div class="icons">
         <?php
            $total_wishlist_counts = 0;
            $total_cart_counts = 0;
            if($user_id != ''){
               $count_wishlist_items = $conn->prepare("SELECT COUNT(*) FROM `wishlist` WHERE user_id = ?");
               $count_wishlist_items->execute([$user_id]);
               $total_wishlist_counts = $count_wishlist_items->fetchColumn();

               $count_cart_items = $conn->prepare("SELECT COUNT(*) FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);
               $total_cart_counts = $count_cart_items->fetchColumn();
            }
         ?>
         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="search_page.php"><i class="fas fa-search"></i></a>
         <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $total_wishlist_counts; ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_counts; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div class="theme-switch-wrapper">
            <label class="theme-switch" for="dark-mode-toggle">
               <input type="checkbox" id="dark-mode-toggle" />
               <div class="slider"></div>
            </label>
         </div>
      </div>

      <div class="profile">
         <?php          
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <div class="profile-info">
            <div class="avatar-frame">
               <i class="fas fa-user-shield"></i>
            </div>
            <p class="member-label">Prestigious Member</p>
            <p class="name"><?= $fetch_profile["name"]; ?></p>
         </div>
         
         <div class="profile-actions">
            <a href="update_user.php" class="btn premium-btn">MEMBER SETTINGS.</a>
            <div class="flex-btn">
               <a href="user_register.php" class="option-btn">Register.</a>
               <a href="user_login.php" class="option-btn">Login.</a>
            </div>
            <a href="components/user_logout.php" class="logout-link" onclick="return confirm('logout from the boutique?');">
               <i class="fas fa-sign-out-alt"></i> Sign out
            </a>
         </div>
         <?php
            }else{
         ?>
         <div class="profile-info">
            <div class="avatar-frame">
               <i class="fas fa-user-circle"></i>
            </div>
            <p class="member-label">Boutique Guest</p>
            <p class="heading-small" style="font-size: 1.6rem; color: var(--black); margin-top: 1rem;">Welcome to sta couture</p>
         </div>
         <div class="profile-actions">
            <div class="flex-btn">
               <a href="user_register.php" class="option-btn">Register</a>
               <a href="user_login.php" class="option-btn">Login</a>
            </div>
         </div>
         <?php
            }
         ?>      
      </div>

   </section>

</header>