<?php
include_once "connection.php";

$product_name  = isset($_POST["name"]) ? trim(htmlentities($_POST["name"])) : '';
$brand_name    = isset($_POST["b_name"]) ? trim($_POST["b_name"]) : '';
$category_name = isset($_POST["category_name"]) ? trim($_POST["category_name"]) : '';
$description   = isset($_POST["description"]) ? trim(htmlentities($_POST["description"])) : '';
$price         = isset($_POST["price"]) ? (float) trim($_POST["price"]) : 0;
$stock_qty     = isset($_POST["quantity"]) ? (int) trim($_POST["quantity"]) : 0;

if ($product_name === '') {
    die("Product name cannot be empty.");
}

$stmt = $con->prepare("INSERT INTO products (p_name, brand_id, category_id, description, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siisdi", $product_name, $brand_name, $category_name, $description, $price, $stock_qty);
$stmt->execute();
$stmt->close();
$con->close();

header("Location: products.php?success=1");
exit;
?>
