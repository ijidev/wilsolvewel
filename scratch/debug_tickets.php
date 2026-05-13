<?php
include 'config.php';
$conn = get_db_connection();
$conn->query("UPDATE tickets SET order_id = 1, subject = 'Inquiry regarding Order #ORD-E8EF28' WHERE id = 1");
echo "Updated ticket 1 to order 1\n";
?>
