<?php
session_start();

include_once "connection.php";
$id = htmlentities($_POST["sale_id"]);
$pid = htmlentities($_POST["product_id"]);
$quantity = htmlentities($_POST["quantity"]);
$price = htmlentities($_POST["unit_price"]);
$tprice = htmlentities($_POST["total_price"]);
$discount = htmlentities($_POST["discount"]);
$tax = htmlentities($_POST["tax"]);
$sql = "insert into sales_item (sale_id, product_id, quantity, unit_Price, total_price, discount, tax) values ('$id', '$pid', '$quantity', '$price', '$tprice','$discount', '$tax')";
$result = mysqli_query($con, $sql);
if($result)
{
    header("location:sales_item.php");
}
else
{
    echo mysqli_error($con);
}