<?php
include_once "connection.php";
$sale_id = $_POST["sale_id"];
$date = $_POST["return_date"];
$status = $_POST["return_status"];
$refund = $_POST["total_refund"];


$sql = "insert into returns (sale_id, return_date, return_status, total_refund) values ( $sale_id, $date, '$status', $refund)";
$result = mysqli_query($con, $sql);
if($result)
{
    header("location:returns.php");
}
else
{
    echo mysqli_error($con);
}