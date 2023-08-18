<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

VerifyPersonsTable($connection, DB_DATABASE);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['operacao']=="criar") {
    $name = mysqli_real_escape_string($connection, $_POST['Name']);
    $age = mysqli_real_escape_string($connection, $_POST['Age']);
    $email = mysqli_real_escape_string($connection, $_POST['Email']);

    if (!empty($name)) {
        AddPerson($connection, $name, $age, $email);
    }
}else if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['operacao']=="editar") {
    $id = mysqli_real_escape_string($connection, $_POST['ID']);
    $newName = mysqli_real_escape_string($connection, $_POST['EditName']);
    $newAge = mysqli_real_escape_string($connection, $_POST['EditAge']);
    $newEmail = mysqli_real_escape_string($connection, $_POST['EditEmail']);

    UpdatePerson($connection, $id, $newName, $newAge, $newEmail);
}else if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['operacao']=="deletar") {

    error_log("Teste de mensagem de depuração");
    $id = mysqli_real_escape_string($connection, $_POST['id']);

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

function UpdatePerson($connection, $id, $newName, $newAge, $newEmail) {
    $query = "UPDATE Persons SET Name='$newName', Age='$newAge', Email='$newEmail' WHERE ID='$id'";

    if (!mysqli_query($connection, $query)) {
        echo("Error updating person data.");
    }
}

function VerifyPersonsTable($connection, $dbName) {
    if (!TableExists("Persons", $connection, $dbName)) {
        $query = "CREATE TABLE Persons (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Name VARCHAR(50) NOT NULL,
            Age INT,
            Email VARCHAR(100)
        )";

        if (!mysqli_query($connection, $query)) {
            echo("<p>Error creating table.</p>");
        }
    }
}

function AddPerson($connection, $name, $age, $email) {
    $query = "INSERT INTO Persons (Name, Age, Email) VALUES ('$name', '$age', '$email');";

    if (!mysqli_query($connection, $query)) {
        echo("<p>Error adding person data.</p>");
    }
}

function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    return mysqli_num_rows($checktable) > 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Person Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            text-align: center;
            margin: 20px 0;
        }
        form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .edit-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
        .edit-buttons button {
            padding: 5px 10px;
            cursor: pointer;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .edit-buttons button:hover {
            background-color: #2980b9;
        }
        .edit-buttons button:active {
            transform: scale(0.95);
        }
        .edit-form {
            display: none;
            margin-top: 10px;
        }
        .edit-form input {
            width: 100%;
            padding: 5px;
        }
    </style>
</head>
<body>
<h1>Person Manager</h1>

<!-- Input form -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <label for="Name">Name:</label>
    <input type="text" name="Name" required><br><br>

    <label for="Age">Age:</label>
    <input type="number" name="Age"><br><br>

    <label for="Email">Email:</label>
    <input type="email" name="Email"><br><br>
    <input type="hidden" value="criar" name="operacao">
    <input type="submit" value="Add Person">

</form>

<!-- Display table data -->
<h2>Persons List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php
    $result = mysqli_query($connection, "SELECT * FROM Persons");

    while ($query_data = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>", $query_data['ID'], "</td>",
        "<td>", $query_data['Name'], "</td>",
        "<td>", $query_data['Age'], "</td>",
        "<td>", $query_data['Email'], "</td>";
        echo "<td class='edit-buttons'>
                  <button class='edit-button' data-id='" . $query_data['ID'] . "'>Edit</button>
                  <button class='delete-button' data-id='" . $query_data['ID'] . "'>Delete</button>
              </td>";
        echo "</tr>";
    }

    mysqli_free_result($result);
    mysqli_close($connection);
    ?>
</table>

<!-- Edit form -->
<div class="edit-form" id="editForm">
    <h3>Edit Person</h3>
    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
        <input type="hidden" name="ID" id="editID">
        <label for="EditName">Name:</label>
        <input type="text" name="EditName" id="editName" required><br><br>

        <label for="EditAge">Age:</label>
        <input type="number" name="EditAge" id="editAge"><br><br>

        <label for="EditEmail">Email:</label>
        <input type="email" name="EditEmail" id="editEmail"><br><br>

        <input type="hidden" value="editar" name="operacao">
        <button id="cancelEdit">Cancel</button>
        <input type="submit" value="Save">
        
    </form>
</div>

<div style="display: none;">
    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" id="deleteForm">
        <input type="hidden" value="deletar" name="operacao">
        <input type="hidden" id="idPerson" name="id">
        <button type="submit">Submit</button>
    </form>
</div>

<script>
    const editButtons = document.querySelectorAll('.edit-button');
    const deleteButtons = document.querySelectorAll('.delete-button');
    const editForm = document.getElementById('editForm');
    const editID = document.getElementById('editID');
    const editName = document.getElementById('editName');
    const editAge = document.getElementById('editAge');
    const editEmail = document.getElementById('editEmail');
    const cancelEdit = document.getElementById('cancelEdit');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            editID.value = id;
            editName.value = button.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.textContent;
            editAge.value = button.parentElement.previousElementSibling.previousElementSibling.textContent;
            editEmail.value = button.parentElement.previousElementSibling.textContent;
            editForm.style.display = 'block';
        });
    });


    cancelEdit.addEventListener('click', () => {
        editForm.style.display = 'none';
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this person?')) {   
                document.getElementById("idPerson").value = id
                document.getElementById("deleteForm").submit();
            }
        });
    });

    cancelEdit.addEventListener('click', () => {
        editForm.style.display = 'none';
    });

</script>
</body>
</html>

