<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

VerifyPersonsTable($connection, DB_DATABASE);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connection, $_POST['Name']);
    $age = mysqli_real_escape_string($connection, $_POST['Age']);
    $email = mysqli_real_escape_string($connection, $_POST['Email']);

    if (!empty($name)) {
        AddPerson($connection, $name, $age, $email);
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
        }
        h1, h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
    <form action="#" method="POST">
        <input type="hidden" name="ID" id="editID">
        <label for="EditName">Name:</label>
        <input type="text" name="EditName" id="editName" required><br><br>

        <label for="EditAge">Age:</label>
        <input type="number" name="EditAge" id="editAge"><br><br>

        <label for="EditEmail">Email:</label>
        <input type="email" name="EditEmail" id="editEmail"><br><br>

        <div class="edit-buttons">
            <button id="cancelEdit">Cancel</button>
            <input type="submit" value="Save">
        </div>
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

    // Adicionar evento de envio de formulário de edição
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar o envio padrão do formulário

        const id = editID.value;
        const newName = editName.value;
        const newAge = editAge.value;
        const newEmail = editEmail.value;

        // Enviar dados do formulário de edição para o backend
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'Edit.php', true); // Substitua 'edit.php' pela URL correta
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Atualizar a tabela com os dados atualizados
                    updateTable();
                    editForm.style.display = 'none'; // Esconder o formulário de edição
                } else {
                    alert('Error editing person.'); // Lidar com erro, se necessário
                }
            }
        };
        xhr.send(`ID=${id}&EditName=${newName}&EditAge=${newAge}&EditEmail=${newEmail}`);
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this person?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `Delete.php?id=${id}`, true); // Substitua 'delete.php' pela URL correta
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Atualizar a tabela removendo o registro excluído
                            updateTable();
                        } else {
                            alert('Error deleting person.'); // Lidar com erro, se necessário
                        }
                    }
                };
                xhr.send();
            }
        });
    });

    cancelEdit.addEventListener('click', () => {
        editForm.style.display = 'none';
    });

    // Função para atualizar a tabela com os dados mais recentes
    function updateTable() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'GetData.php', true); // Substitua 'get_data.php' pela URL correta para recuperar dados
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Atualizar o conteúdo da tabela com os dados mais recentes
                    const tableBody = document.querySelector('table tbody');
                    tableBody.innerHTML = xhr.responseText;
                } else {
                    alert('Error updating table.'); // Lidar com erro, se necessário
                }
            }
        };
        xhr.send();
    }
</script>
</body>
</html>

