<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

// Get product ID from request
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    echo "<script>
        alert('❌ Invalid product ID.');
        window.location.href = 'products.php';
    </script>";
    exit;
}

// ✅ Delete product by ID
$delete_query = "DELETE FROM products WHERE product_id = ?";
$stmt = $con->prepare($delete_query);

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ✅ Optional: safely recreate temp_products (ignore if already exists)
        $con->query("DROP TABLE IF EXISTS temp_products");
        $create_temp = "
            CREATE TABLE temp_products AS
            SELECT * FROM products ORDER BY product_id ASC
        ";
        $con->query($create_temp);

        echo "<script>
            alert('✅ Product deleted successfully!');
            window.location.href = 'products.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error deleting product: " . addslashes($stmt->error) . "');
            window.history.back();
        </script>";
    }

    $stmt->close();
} else {
    echo "<script>
        alert('❌ Database error: " . addslashes($con->error) . "');
        window.history.back();
    </script>";
}

$con->close();
?>
