<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);
   $message[] = 'payment status updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Placed Orders.</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders">

<h1 class="heading">Placed Orders.</h1>

<div class="box-container">

   <?php
      // Pagination Logic
      $limit = 10;
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $limit;

      $total_orders_query = $conn->prepare("SELECT COUNT(*) FROM `orders`");
      $total_orders_query->execute();
      $total_rows = $total_orders_query->fetchColumn();
      $total_pages = ceil($total_rows / $limit);

      $select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY id DESC LIMIT :limit OFFSET :offset");
      $select_orders->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
      $select_orders->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
      $select_orders->execute();

      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Placed On : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Number : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Address : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Total price : <span>৳<?= $fetch_orders['total_price']; ?>/-</span> </p>
      <p> Payment method : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="select">
            <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
         </select>
        <div class="flex-btn">
         <input type="submit" value="update" class="option-btn" name="update_payment">
         <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
        </div>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
   ?>

</div>

<?php if($total_pages > 1): ?>
<div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
   <?php if($page > 1): ?>
      <a href="placed_orders.php?page=<?= $page - 1; ?>" class="option-btn" style="width: auto;">Prev</a>
   <?php endif; ?>

   <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <a href="placed_orders.php?page=<?= $i; ?>" class="<?= ($i == $page) ? 'btn' : 'option-btn'; ?>" style="width: auto; padding: 1rem 2rem; min-width: 5rem;"><?= $i; ?></a>
   <?php endfor; ?>

   <?php if($page < $total_pages): ?>
      <a href="placed_orders.php?page=<?= $page + 1; ?>" class="option-btn" style="width: auto;">Next</a>
   <?php endif; ?>
</div>
<?php endif; ?>

</section>

</section>












<script src="../js/admin_script.js"></script>
   
</body>
</html>