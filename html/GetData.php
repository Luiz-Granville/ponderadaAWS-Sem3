<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

$database = mysqli_select_db($connection, DB_DATABASE);

$result = mysqli_query($connection, "SELECT * FROM Persons");

$output = '<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Action</th>
          </tr>';

while ($query_data = mysqli_fetch_assoc($result)) {
    $output .= '<tr>';
    $output .= '<td>' . $query_data['ID'] . '</td>';
    $output .= '<td>' . $query_data['Name'] . '</td>';
    $output .= '<td>' . $query_data['Age'] . '</td>';
    $output .= '<td>' . $query_data['Email'] . '</td>';
    $output .= '<td class="edit-buttons">
                   <button class="edit-button" data-id="' . $query_data['ID'] . '">Edit</button>
                   <button class="delete-button" data-id="' . $query_data['ID'] . '">Delete</button>
               </td>';
    $output .= '</tr>';
}

mysqli_free_result($result);
mysqli_close($connection);

echo $output;
?>
