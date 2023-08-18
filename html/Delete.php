<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

$database = mysqli_select_db($connection, DB_DATABASE);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connection, $_GET['id']);

    $query = "DELETE FROM Persons WHERE ID = $id";

    if (mysqli_query($connection, $query)) {
        // Successful deletion
        header("Location: {$_SERVER['HTTP_REFERER']}"); // Redirect back to the previous page
        exit();
    } else {
        // Error deleting record
        echo "Error deleting record: " . mysqli_error($connection);
    }
}

mysqli_close($connection);
?>
