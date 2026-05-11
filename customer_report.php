<?php
session_start();
include_once "connection.php";

// Initialize $customer
$customer = null;

// Get customer ID from URL
if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    if($id == 0){
        // Walk-in Customer default data
        $customer = [
            'name' => 'Walk-in Customer',
            'email' => 'customer@tecdiary.com',
            'phone' => '0123456789',
            'address' => 'Customer Address',
            'city' => 'Khanpur',
            'state' => 'Punjab',
            'postal_code' => '46000',
            'country' => 'Pakistan'
        ];
    } else {
        // Fetch from your actual customer table
        $query = "SELECT name, email, phone, address FROM customer WHERE customer_id = $id LIMIT 1";
        $result = mysqli_query($con, $query);

        if($result && mysqli_num_rows($result) > 0){
            $data = mysqli_fetch_assoc($result);
            $customer = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => '',
                'state' => '',
                'postal_code' => '',
                'country' => ''
            ];
        } else {
            echo "<h3>Customer not found.</h3>";
            exit;
        }
    }
} else {
    echo "<h3>Customer ID not provided.</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Report - <?php echo htmlspecialchars($customer['name']); ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .report-container { background: #fff; padding: 30px; margin-top: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; color: #333; }
        .section-header { background: #007bff; color: #fff; padding: 8px 12px; border-radius: 4px; margin-bottom: 10px; }
        .table th { width: 180px; background: #f8f9fa; }
        .btn-print { float: right; }
        @media print {
            .btn, .btn-print, a.btn { display: none !important; }
        }
    </style>
</head>
<body>
<div class="container report-container">
    <div class="d-flex justify-content-between mb-3">
        <h2>Customer Report: <?php echo htmlspecialchars($customer['name']); ?></h2>
        <div>
            <a href="add_sales.php" class="btn btn-secondary">Back to Add Sale</a>
            <button onclick="window.print();" class="btn btn-primary btn-print">Print Report</button>
        </div>
    </div>

    <!-- Customer Details Section -->
    <div class="section-header">Customer Details</div>
    <table class="table table-bordered table-striped">
        <tr><th>Name</th><td><?php echo htmlspecialchars($customer['name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($customer['email']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($customer['phone']); ?></td></tr>
        <tr><th>Address</th><td><?php echo htmlspecialchars($customer['address']); ?></td></tr>
        <tr><th>City</th><td><?php echo htmlspecialchars($customer['city']); ?></td></tr>
        <tr><th>State</th><td><?php echo htmlspecialchars($customer['state']); ?></td></tr>
        <tr><th>Postal Code</th><td><?php echo htmlspecialchars($customer['postal_code']); ?></td></tr>
        <tr><th>Country</th><td><?php echo htmlspecialchars($customer['country']); ?></td></tr>
    </table>

    <!-- View Sales History Button -->
    <a href="customer_sales_history.php?id=<?php echo $id; ?>" class="btn btn-info mt-3">
        View Sales History
    </a>
</div>
</body>
</html>
