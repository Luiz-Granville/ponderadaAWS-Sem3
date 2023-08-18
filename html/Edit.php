<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($connection, $_POST['ID']);
    $newName = mysqli_real_escape_string($connection, $_POST['EditName']);
    $newAge = mysqli_real_escape_string($connection, $_POST['EditAge']);
    $newEmail = mysqli_real_escape_string($connection, $_POST['EditEmail']);

    UpdatePerson($connection, $id, $newName, $newAge, $newEmail);
}

function UpdatePerson($connection, $id, $newName, $newAge, $newEmail) {
    $query = "UPDATE Persons SET Name='$newName', Age='$newAge', Email='$newEmail' WHERE ID='$id'";

    if (!mysqli_query($connection, $query)) {
        echo("Error updating person data.");
    }
}
?>
