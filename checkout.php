<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);

   // Manual Payment Verification Data
   if($method == 'bkash' || $method == 'nagad' || $method == 'rocket'){
       if(isset($_POST['trx_id']) && isset($_POST['sender_number'])){
           $trx_id = filter_var($_POST['trx_id'], FILTER_SANITIZE_STRING);
           $sender_number = filter_var($_POST['sender_number'], FILTER_SANITIZE_STRING);
           $method = $method . ' (TrxID: ' . $trx_id . ', Sender: ' . $sender_number . ')';
       }
   }
   $address = 'House/Flat: '. $_POST['flat'] .', Road/Area: '. $_POST['street'] .', District: '. $_POST['city'] .', Division: '. $_POST['state'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $sub_total = $_POST['total_price'];
   
   // Calculate delivery charge on backend for security
   $district = $_POST['city'] ?? '';
   $delivery_charge = (strtolower($district) == 'dhaka') ? 80 : 130;
   $total_price = $sub_total + $delivery_charge;

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
   }else{
      $message[] = 'your cart is empty';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-portal">

   <div class="luxury-form-container">
      <form action="" method="POST" class="luxury-form cinematic-grid">
         <h2 class="luxury-heading">Checkout</h2>
         <p class="form-tagline">Complete your order</p>

         <div class="checkout-main-content">
            <div class="billing-suite">
               <h3 class="suite-title">Identity & Payment</h3>
               <div class="form-grid">
                  <div class="input-group">
                     <span>Full Name</span>
                     <input type="text" name="name" placeholder="Your Name" class="box" maxlength="50" required>
                  </div>
                  <div class="input-group">
                     <span>Mobile Number</span>
                     <input type="number" name="number" placeholder="01XXXXXXXXX" class="box" min="0" max="99999999999" onkeypress="if(this.value.length == 11) return false;" required>
                  </div>
                  <div class="input-group">
                     <span>Email Address</span>
                     <input type="email" name="email" placeholder="Your Email" class="box" maxlength="50" required>
                  </div>
                  <div class="input-group">
                     <span>Payment Method</span>
                     <select name="method" id="payment-method" class="box" required>
                        <option value="cash on delivery">Cash on Delivery</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="rocket">Rocket</option>
                     </select>
                  </div>
               </div>

               <!-- Dynamic Payment Details -->
               <div id="payment-details-box" class="payment-instructions-suite" style="display:none;">
                  <div class="boutique-alert">
                     <i class="fas fa-crown"></i> 
                     <span>Transfer Amount to <strong id="merchant-number">017XXXXXXXX</strong></span>
                  </div>
                  <div class="form-grid compact">
                     <div class="input-group">
                        <span>Sender Number</span>
                        <input type="number" name="sender_number" id="sender_number" placeholder="01XXXXXXXXX" class="box">
                     </div>
                     <div class="input-group">
                        <span>Transaction ID</span>
                        <input type="text" name="trx_id" id="trx_id" placeholder="TrxID (e.g. 8N7A6D5...)" class="box">
                     </div>
                  </div>
               </div>

               <h3 class="suite-title" style="margin-top: 4rem;">Shipping Details</h3>
               <div class="form-grid">
                  <div class="input-group">
                     <span>Flat / House No.</span>
                     <input type="text" name="flat" placeholder="e.g. Flat 3B, House 12" class="box" maxlength="50" required>
                  </div>
                  <div class="input-group">
                     <span>Road / Area / Address</span>
                     <input type="text" name="street" placeholder="e.g. Road 5, Block B" class="box" maxlength="50" required>
                  </div>
                  <div class="input-group">
                     <span>District</span>
                     <select name="city" id="district-select" class="box" required>
                        <option value="" disabled selected>Select District</option>
                        <option value="Dhaka">Dhaka (Central)</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Gazipur">Gazipur</option>
                        <option value="Narayanganj">Narayanganj</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Khulna">Khulna</option>
                        <option value="Barisal">Barisal</option>
                        <option value="Rangpur">Rangpur</option>
                        <option value="Mymensingh">Mymensingh</option>
                        <option value="Comilla">Comilla</option>
                        <option value="Brahmanbaria">Brahmanbaria</option>
                        <option value="Noakhali">Noakhali</option>
                        <option value="Feni">Feni</option>
                        <option value="Chandpur">Chandpur</option>
                        <option value="Lakshmipur">Lakshmipur</option>
                        <option value="Cox's Bazar">Cox's Bazar</option>
                        <option value="Tangail">Tangail</option>
                        <option value="Munshiganj">Munshiganj</option>
                        <option value="Manikganj">Manikganj</option>
                        <option value="Narsingdi">Narsingdi</option>
                        <option value="Madaripur">Madaripur</option>
                        <option value="Shariatpur">Shariatpur</option>
                        <option value="Rajbari">Rajbari</option>
                        <option value="Gopalganj">Gopalganj</option>
                        <option value="Faridpur">Faridpur</option>
                        <option value="Kishoreganj">Kishoreganj</option>
                        <option value="Netrokona">Netrokona</option>
                        <option value="Sherpur">Sherpur</option>
                        <option value="Jamalpur">Jamalpur</option>
                        <option value="Pabna">Pabna</option>
                        <option value="Natore">Natore</option>
                        <option value="Sirajganj">Sirajganj</option>
                        <option value="Bogra">Bogra</option>
                        <option value="Joypurhat">Joypurhat</option>
                        <option value="Naogaon">Naogaon</option>
                        <option value="Chapai Nawabganj">Chapai Nawabganj</option>
                        <option value="Dinajpur">Dinajpur</option>
                        <option value="Thakurgaon">Thakurgaon</option>
                        <option value="Panchagarh">Panchagarh</option>
                        <option value="Kurigram">Kurigram</option>
                        <option value="Lalmonirhat">Lalmonirhat</option>
                        <option value="Gaibandha">Gaibandha</option>
                        <option value="Nilphamari">Nilphamari</option>
                        <option value="Jessore">Jessore</option>
                        <option value="Jhenaidah">Jhenaidah</option>
                        <option value="Magura">Magura</option>
                        <option value="Narail">Narail</option>
                        <option value="Satkhira">Satkhira</option>
                        <option value="Bagerhat">Bagerhat</option>
                        <option value="Kushtia">Kushtia</option>
                        <option value="Meherpur">Meherpur</option>
                        <option value="Chuadanga">Chuadanga</option>
                        <option value="Pirojpur">Pirojpur</option>
                        <option value="Jhalokathi">Jhalokathi</option>
                        <option value="Patuakhali">Patuakhali</option>
                        <option value="Bhola">Bhola</option>
                        <option value="Barguna">Barguna</option>
                        <option value="Habiganj">Habiganj</option>
                        <option value="Moulvibazar">Moulvibazar</option>
                        <option value="Sunamganj">Sunamganj</option>
                        <option value="Khagrachhari">Khagrachhari</option>
                        <option value="Rangamati">Rangamati</option>
                        <option value="Bandarban">Bandarban</option>
                     </select>
                  </div>
                  <div class="input-group">
                     <span>Division / State</span>
                     <input type="text" name="state" placeholder="e.g. Dhaka" class="box" maxlength="50" required>
                  </div>
               </div>
            </div>

            <div class="checkout-sidebar">
               <div class="acquisition-summary-box">
                  <h3 class="summary-title">Order Summary</h3>
                  <div class="item-ledger">
                     <?php
                        $grand_total = 0;
                        $cart_items = [];
                        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                        $select_cart->execute([$user_id]);
                        if($select_cart->rowCount() > 0){
                           while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                              $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
                              $total_products = implode($cart_items);
                              $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                     ?>
                     <div class="ledger-item">
                        <div class="p-info">
                           <span class="p-name"><?= $fetch_cart['name']; ?></span>
                           <span class="p-qty">x<?= $fetch_cart['quantity']; ?></span>
                        </div>
                        <span class="p-price">৳<?= $fetch_cart['price'] * $fetch_cart['quantity']; ?></span>
                     </div>
                     <?php
                           }
                        }
                     ?>
                  </div>
                  
                  <input type="hidden" name="total_products" value="<?= $total_products; ?>">
                  <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

                  <div class="summary-calculations">
                     <div class="calc-row">
                        <span>Subtotal</span>
                        <span>৳<?= $grand_total; ?></span>
                     </div>
                     <div class="calc-row">
                        <span>Delivery Charge</span>
                        <span id="delivery-display" class="gold-text">৳0</span>
                     </div>
                     <div class="divider"></div>
                     <div class="calc-row grand-total">
                        <span>Grand Total</span>
                        <span id="grand-total-display" data-subtotal="<?= $grand_total; ?>">৳<?= $grand_total; ?></span>
                     </div>
                  </div>

                  <input type="submit" name="order" class="btn premium-btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="PLACE ORDER">
                  
                  <div class="secure-checkout-seal">
                     <i class="fas fa-shield-alt"></i> <span>Secure Checkout</span>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>

</section>

   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<script>
   let districtSelect = document.getElementById('district-select');
   let deliveryDisplay = document.getElementById('delivery-display');
   let grandTotalDisplay = document.getElementById('grand-total-display');
   let subtotal = parseInt(grandTotalDisplay.getAttribute('data-subtotal'));


   let paymentMethod = document.getElementById('payment-method');
   let paymentDetailsBox = document.getElementById('payment-details-box');
   let senderInput = document.getElementById('sender_number');
   let trxInput = document.getElementById('trx_id');
   let merchantNumberDisplay = document.getElementById('merchant-number');

   // Merchant Numbers
   const merchantNumbers = {
       'bkash': '01612684181',
       'nagad': '01612684181',
       'rocket': '01612684181'
   };

   paymentMethod.addEventListener('change', function() {
      if(this.value === 'bkash' || this.value === 'nagad' || this.value === 'rocket') {
         paymentDetailsBox.style.display = 'block';
         senderInput.setAttribute('required', 'required');
         trxInput.setAttribute('required', 'required');
         
         // Update merchant number dynamically based on selection
         merchantNumberDisplay.innerText = merchantNumbers[this.value];
         
      } else {
         paymentDetailsBox.style.display = 'none';
         senderInput.removeAttribute('required');
         trxInput.removeAttribute('required');
      }
   });

   districtSelect.addEventListener('change', function() {
      let selectedDistrict = this.value;
      let charge = (selectedDistrict === 'Dhaka') ? 80 : 130;
      
      deliveryDisplay.innerText = '৳' + charge + '/-';
      grandTotalDisplay.innerText = '৳' + (subtotal + charge) + '/-';
      
      // Animate the update
      grandTotalDisplay.style.color = 'var(--accent)';
      setTimeout(() => {
         grandTotalDisplay.style.color = 'var(--black)';
      }, 500);
   });
</script>

</body>
</html>