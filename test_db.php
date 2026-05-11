<?php
include_once "connection.php";

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h3>Database Connection: Success!</h3>";

$query = "SELECT username, email, role, password FROM users";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'><tr><th>Username</th><th>Email</th><th>Role</th><th>Password (Plain)</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>".$row['username']."</td><td>".$row['email']."</td><td>".$row['role']."</td><td>".$row['password']."</td></tr>";
    }
    echo "</table>";
} else {
    echo "The 'users' table is EMPTY. You need to register a user first.";
}
?>
