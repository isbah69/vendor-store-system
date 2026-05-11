<?php
include_once "connection.php";

if (isset($_POST['query'])) {

    $query = mysqli_real_escape_string($con, htmlentities($_POST['query']));

    $sql = "SELECT customer_id, name, email FROM customer WHERE name LIKE '%$query%' LIMIT 5";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) > 0) {
        echo '<ul class="list-group">';
        while($row = mysqli_fetch_assoc($result)) {
            echo '<li class="list-group-item list-group-item-action" data-id="'.$row['customer_id'].'">'.$row['name'].'</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="text-muted p-2">No customer found</p>';
    }
}
?>
