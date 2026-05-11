<?php
include_once "connection.php";

// Get form data
$company_id   = $_POST['company_id'];
$sale_date    = $_POST['sale_date'];
$total_amount = $_POST['total_amount'];
$product_ids  = $_POST['product_id'] ?? [];
$prices       = $_POST['price'] ?? [];
$qtys         = $_POST['qty'] ?? [];

// ✅ First insert main sale record into `sales` table
$sql = "INSERT INTO sales (company_id, sale_date, total_amount) VALUES ('$company_id', '$sale_date', '$total_amount')";
if (mysqli_query($con, $sql)) {
    $sale_id = mysqli_insert_id($con); // get the sale_id we just created

    // ✅ Loop through each product
    for ($i = 0; $i < count($product_ids); $i++) {
        $pid = $product_ids[$i];
        $price = $prices[$i];
        $qty = $qtys[$i];
        $subtotal = $price * $qty;

        // ✅ Check available stock
        $check = mysqli_query($con, "SELECT stock_quantity FROM products WHERE product_id = '$pid'");
        $row = mysqli_fetch_assoc($check);
        $current_stock = $row['stock_quantity'];

        if ($current_stock < $qty) {
            echo "⚠️ Not enough stock for product ID: $pid";
            exit;
        }

        // ✅ Insert into sales_item table (your actual table)
        $sql_item = "INSERT INTO sales_item (sale_id, product_id, quantity, unit_price, total_price) 
                     VALUES ('$sale_id', '$pid', '$qty', '$price', '$subtotal')";
        mysqli_query($con, $sql_item);

        // ✅ Update stock in products table
        $sql_stock = "UPDATE products SET stock_quantity = stock_quantity - $qty WHERE product_id = '$pid'";
        mysqli_query($con, $sql_stock);
    }

    // ✅ Redirect to sales page after successful insert
    // ✅ Redirect with sale_id
    header("Location: sales.php?success=1");
exit;
    

} else {
    echo "❌ Error: " . mysqli_error($con);
}
?>
