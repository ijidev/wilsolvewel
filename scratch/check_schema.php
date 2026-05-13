<?php
$conn = mysqli_connect('localhost', 'root', '', 'wilsolvewel_db');
$res = $conn->query('DESCRIBE procurement_history');
while($r = $res->fetch_assoc()) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}
echo "----\n";
$res = $conn->query('DESCRIBE procurement_orders');
while($r = $res->fetch_assoc()) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}
echo "----\n";
$res = $conn->query('DESCRIBE tickets');
while($r = $res->fetch_assoc()) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}
?>
