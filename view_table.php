<?php
include 'auth_check.php';

$table = $_GET['table'] ?? '';

if (!canAccessTable($table)) {
    die("Access denied for this table!");
}

$result = $conn->query("SELECT * FROM $table");

echo "<h3>".ucfirst(str_replace('_',' ',$table))."</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
if ($result->num_rows > 0) {
    // Headers
    echo "<tr>";
    while ($field = $result->fetch_field()) {
        echo "<th>".$field->name."</th>";
    }
    echo "</tr>";

    // Rows
    $result->data_seek(0);
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        foreach($row as $col){
            echo "<td>".htmlspecialchars($col)."</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='100%'>No data found</td></tr>";
}
echo "</table>";
?>
