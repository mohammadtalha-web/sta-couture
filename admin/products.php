<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $p_code = $_POST['p_code'];
   $p_code = filter_var($p_code, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   // Use Absolute Path for Linux/Pro Hosting Compatibility
   $image_folder_01 = dirname(__DIR__) . '/uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = dirname(__DIR__) . '/uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = dirname(__DIR__) . '/uploaded_img/' . $image_03;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'product name already exist!';
   }else{
      $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
      $ext_01 = pathinfo($image_01, PATHINFO_EXTENSION);
      $ext_02 = pathinfo($image_02, PATHINFO_EXTENSION);
      $ext_03 = pathinfo($image_03, PATHINFO_EXTENSION);

      if(!in_array(strtolower($ext_01), $allowed_ext) OR !in_array(strtolower($ext_02), $allowed_ext) OR !in_array(strtolower($ext_03), $allowed_ext)){
         $message[] = 'invalid image format!';
      }elseif($image_size_01 > 2000000 OR $image_size_02 > 2000000 OR $image_size_03 > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $insert_products = $conn->prepare("INSERT INTO `products`(name, p_code, details, category, price, image_01, image_02, image_03) VALUES(?,?,?,?,?,?,?,?)");
         $insert_products->execute([$name, $p_code, $details, $category, $price, $image_01, $image_02, $image_03]);

         if($insert_products){
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'new product added!';
         }
      }
   }  

};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image_01']);
   unlink('../uploaded_img/'.$fetch_delete_image['image_02']);
   unlink('../uploaded_img/'.$fetch_delete_image['image_03']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   header('location:products.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">Add Product</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <span>Product Name (required)</span>
            <input type="text" class="box" required maxlength="100" placeholder="enter product name" name="name">
         </div>
         <div class="inputBox">
            <span>Product Price (required)</span>
            <input type="number" min="0" class="box" required max="9999999999" placeholder="enter product price" onkeypress="if(this.value.length == 10) return false;" name="price">
         </div>
         <div class="inputBox">
            <span>Product Code (required)</span>
            <input type="text" class="box" required maxlength="20" placeholder="enter product code" name="p_code">
         </div>
        <div class="inputBox">
            <span>Image 01 (required)</span>
            <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
        </div>
        <div class="inputBox">
            <span>Image 02 (required)</span>
            <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
        </div>
        <div class="inputBox">
            <span>Image 03 (required)</span>
            <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
        </div>
         <div class="inputBox">
            <span>Product description (required)</span>
            <textarea name="details" placeholder="enter product details" class="box" required maxlength="500" cols="30" rows="10"></textarea>
         </div>
         <div class="inputBox">
            <span>Product category (required)</span>
            <select name="category" class="box" required>
               <option value="" disabled selected>select category --</option>
               <option value="women">Women's Dress</option>
               <option value="men">Men's Dress</option>
               <option value="three">Three Pieces</option>
               <option value="borkha">Borkha</option>
               <option value="saree">Saree</option>
               <option value="shoes">Shoes</option>
            </select>
         </div>
      </div>
      
      <input type="submit" value="add product" class="btn" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="heading">Products Added.</h1>

   <div class="box-container">

   <?php
      // Pagination Logic
      $limit = 10;
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
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="" loading="lazy">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="price">৳<span><?= $fetch_products['price']; ?></span>/-</div>
      <div class="details">Code: <span><?= $fetch_products['p_code']; ?></span></div>
      <div class="details">Category: <span><?= $fetch_products['category']; ?></span></div>
      <div class="details"><span><?= $fetch_products['details']; ?></span></div>
      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>
   
   </div>

   <?php if($total_pages > 1): ?>
   <div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
      <?php if($page > 1): ?>
         <a href="products.php?page=<?= $page - 1; ?>" class="option-btn" style="width: auto;">Prev</a>
      <?php endif; ?>

      <?php for($i = 1; $i <= $total_pages; $i++): ?>
         <a href="products.php?page=<?= $i; ?>" class="<?= ($i == $page) ? 'btn' : 'option-btn'; ?>" style="width: auto; padding: 1rem 2rem; min-width: 5rem;"><?= $i; ?></a>
      <?php endfor; ?>

      <?php if($page < $total_pages): ?>
         <a href="products.php?page=<?= $page + 1; ?>" class="option-btn" style="width: auto;">Next</a>
      <?php endif; ?>
   </div>
   <?php endif; ?>

</section>








<script src="../js/admin_script.js"></script>
   
</body>
</html>