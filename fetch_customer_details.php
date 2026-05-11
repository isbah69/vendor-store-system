<?php
include_once "connection.php";

if(isset($_POST['customer_id'])) {
    $id = intval($_POST['customer_id']); // sanitize input

    // Correct column name: customer_id
    $query = "SELECT * FROM customer WHERE customer_id = $id LIMIT 1";
    $result = mysqli_query($con, $query);

    if($row = mysqli_fetch_assoc($result)) {
        // Return JSON for JS
        echo json_encode([
            'status' => 'success',
            'customer_id' => $row['customer_id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'city' => $row['city'],
            'country' => $row['country'],
            'state' => $row['state'],
            'postal_code' => $row['postal_code'],
            'company' => $row['company'] ?? '',
            'customer_group' => $row['customer_group'] ?? 'General',
            'vat_number' => $row['vat_number'] ?? '',
            'gst_number' => $row['gst_number'] ?? '',
            'deposit' => $row['deposit'] ?? '0.00',
            'award_points' => $row['award_points'] ?? 0
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
    }
}
?>
