<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];
$sql = "delete from returns_item where return_item_id = $id";
$result = mysqli_query($con, $sql);
if(mysqli_affected_rows($con) > 0)
{
    header("location:returns_item.php");
}
else
{
    echo mysqli_error($con);
}