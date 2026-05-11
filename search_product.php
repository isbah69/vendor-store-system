<?php


include_once "connection.php";
// ✅ Correct isset usage
     if (isset($_POST['query'])) {
    $search = mysqli_real_escape_string($con, htmlentities($_POST['query']));
    
    // Use correct column names: product_id and p_name
    $query = "SELECT * FROM products WHERE p_name LIKE '%$search%' LIMIT 10";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='search-item' 
                    data-id='{$row['product_id']}' 
                    data-name='{$row['p_name']}' 
                    data-price='{$row['price']}'>
                    {$row['p_name']} - ₨ {$row['price']}
                  </div>";
        }
    } else {
        echo "<div class='text-muted p-2'>No products found</div>";
    }
}
?>
