<?php

// Check if running on localhost or direct IP
$is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || ($_SERVER['SERVER_NAME'] ?? '') == 'localhost';

if ($is_local) {
   // Local Development Configuration
   $db_name = 'mysql:host=localhost;dbname=shop_db';
   $user_name = 'root';
   $user_password = '';
} else {
   // Production Maison Configuration (InfinityFree)
   $db_name = 'mysql:host=sql100.infinityfree.com;dbname=if0_41046441_shop_db';
   $user_name = 'if0_41046441';
   $user_password = '8z6nQBA9JFeHDL';
}

try {
   $conn = new PDO($db_name, $user_name, $user_password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   // On production, we might want to log this instead of showing it
   // die('Connection failed. Please check back later.');
}

?>