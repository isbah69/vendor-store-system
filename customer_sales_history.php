<?php

session_start();

include_once "connection.php";

// Get customer ID
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id === null) {
    echo "<h3>Customer ID not provided.</h3>";
    exit;
}

// Get customer name
if ($id == 0) {
    $customer_name = "Walk-in Customer";
} else {
    $result = mysqli_query($con, "SELECT name FROM customers WHERE customer_id = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $customer_name = $row['name'];
    } else {
        echo "<h3>Customer not found.</h3>";
        exit;
    }
}

// Check if 'sales' table has column 'customer_name' (or adjust if different)
$check_column = mysqli_query($con, "SHOW COLUMNS FROM sales LIKE 'customer_name'");
$has_customer_column = mysqli_num_rows($check_column) > 0;

// Fetch sales safely
if ($has_customer_column) {
    $sales_query = "SELECT * FROM sales WHERE customer_name = '".mysqli_real_escape_string($con, $customer_name)."' ORDER BY sale_id DESC";
    $sales_result = mysqli_query($con, $sales_query);
} else {
    // Column does not exist, cannot filter per customer
    $sales_result = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales History - <?php echo htmlspecialchars($customer_name); ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Sales History: <?php echo htmlspecialchars($customer_name); ?></h2>
    <a href="customer_report.php?id=<?php echo $id; ?>" class="btn btn-secondary mb-3">Back to Customer Report</a>

    <?php if ($sales_result && mysqli_num_rows($sales_result) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Sale ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sale = mysqli_fetch_assoc($sales_result)): ?>
                    <tr>
                        <td><?php echo $sale['sale_id']; ?></td>
                        <td><?php echo $sale['sale_date']; ?></td>
                        <td><?php echo $sale['total_amount']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No sales history available for this customer or sales table has no customer reference column.</p>
    <?php endif; ?>
</div>
</body>
</html>
