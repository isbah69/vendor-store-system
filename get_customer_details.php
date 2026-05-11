<?php
include_once 'connection.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id'])) {
    echo json_encode(['status'=>'error','message'=>'No id']);
    exit;
}

$id = intval($_GET['id']);
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid id']);
    exit;
}

// ✅ Fix connection variable
$sql = "SELECT * FROM customer WHERE customer_id = $id LIMIT 1";
$res = mysqli_query($con, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo json_encode(['status'=>'error','message'=>'Customer not found']);
    exit;
}

$row = mysqli_fetch_assoc($res);

$output = [
  'status' => 'success',
  'customer_id' => $row['customer_id'],
  'name' => $row['name'] ?? '',
  'email' => $row['email'] ?? '',
  'phone' => $row['phone'] ?? '',
  'address' => $row['address'] ?? '',
  'city' => $row['city'] ?? '',
  'state' => $row['state'] ?? '',
  'postal_code' => $row['postal_code'] ?? '',
  'country' => $row['country'] ?? '',
  'company' => $row['company'] ?? '',
  'customer_group' => $row['customer_group'] ?? 'General',
  'vat_number' => $row['vat_number'] ?? '',
  'gst_number' => $row['gst_number'] ?? '',
  'deposit' => $row['deposit'] ?? '0.00',
  'award_points' => $row['award_points'] ?? 0,
];

echo json_encode($output);
?>
