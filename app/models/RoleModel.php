<?php

require_once("../../config/db.php");

// AGREGAMOS EL ROL
/* if (isset($_POST["add_rol"]) == "add_rol") {

    $alert = "";

    if (empty($_POST['full_name']) || empty($_POST['description'])) {
        $alert = '<div class="alert alert-danger" role="alert"> Todo los campos son obligatorio </div>';
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $userUpdatedId = $_SESSION['id_user'];
        // $storeId = $_SESSION['storeId'];
        $storeId = $_POST['storeId'];

        $result = 0;

        if (is_string($name) && !empty($name)) {
            $name = MYSQLI->real_escape_string($name);
            $stmt = MYSQLI->prepare("SELECT * FROM ROLES WHERE NAME = ?");
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_array();

            if ($result) {
                $alert = '<div class="alert alert-danger" role="alert">El nombre del role ya existe</div>';
            } else {
                $stmt_insert = MYSQLI->prepare("INSERT INTO ROLES (NAME, DESCRIPTION, userUpdatedId, storeId) VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param('ssii', $name, $description, $userUpdatedId, $storeId);
                $query_insert = $stmt_insert->execute();

                if ($query_insert) {
                    $alert = '<div class="alert alert-primary" role="alert">Rol Registrado</div>';
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al Guardar</div>';
                }
            }
            $stmt->close();
            if (isset($stmt_insert)) {
                $stmt_insert->close();
            }
        }
        MYSQLI->close();
    }
} */

// EDITAMOS EL ROL
/* if (isset($_POST["edit_role"]) == "edit_role") {

    $alert = "";

    if (empty($_POST['name']) || empty($_POST['description'])) {
        $alert = '<p class"error">Todo los campos son requeridos</p>';
    } else {
        $id_role = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $userUpdatedId = $_SESSION['id_user'];
        // $storeId = $_SESSION['storeId'];
        $storeId = $_POST['storeId'];

        $result = 0;
        $sql_update = mysqli_query(MYSQLI, "UPDATE ROLES SET NAME = '$name' , DESCRIPTION = '$description', storeId = $storeId, userUpdatedId = $userUpdatedId WHERE ID = $id_role");

        if ($sql_update) {
            $alert = '<p class"exito">Rol actualizado correctamente</p>';
        } else {
            $alert = '<p class"error">Error al actualizar el role</p>';
        }
    }
} */

function addRole()
{
    $alert = "";

    if (empty($_POST['name']) || empty($_POST['description'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $userCreatedId = $_SESSION['id_user'];
        $branchId = $_SESSION['branchId'];
        /* $storeId = $_SESSION['storeId']; */
        $storeId = $_POST['storeId'];

        $stmt = MYSQLI->prepare("SELECT * FROM ROLES WHERE NAME = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $alert = '<div class="alert alert-danger" role="alert">El nombre ya está registrado con otro rol.</div>';
        } else {
            $stmt_insert = MYSQLI->prepare("INSERT INTO ROLES (NAME, DESCRIPTION, branchId, userCreatedId, storeId) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssiii", $name, $description, $branchId, $userCreatedId, $storeId);

            if ($stmt_insert->execute()) {
                $alert = '<div class="alert alert-primary" role="alert">Rol Registrado.</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al registrar el rol.</div>';
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
    return $alert;
}

function getRoles()
{
    $query = "SELECT * FROM ROLES";
    $result = mysqli_query(MYSQLI, $query);

    if (!$result) {
        die("Query execution error: " . mysqli_error(MYSQLI));
    }

    $roles = [];

    while ($role = mysqli_fetch_assoc($result)) {
        $roles[] = $role;
    }

    mysqli_free_result($result);

    return $roles;
}

function getRoleById($id_role)
{
    if (MYSQLI->connect_error) {
        die("Error de conexión: " . MYSQLI->connect_error);
    }

    $stmt = MYSQLI->prepare("SELECT * FROM ROLES WHERE ID = ?");
    $stmt->bind_param("i", $id_role);
    $stmt->execute();
    $result_role = $stmt->get_result();

    if ($result_role->num_rows == 0) {
        $stmt->close();
        return null;
    }

    $data_role = $result_role->fetch_assoc();
    $stmt->close();

    return $data_role;
}

function editRole()
{
    $alert = "";

    if (empty($_POST['name']) || empty($_POST['description'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } else {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $userUpdatedId = $_SESSION['id_user'];
        /* $storeId = $_SESSION['storeId']; */
        $storeId = $_POST['storeId'];

        $stmt = MYSQLI->prepare("UPDATE ROLES SET NAME = ?, DESCRIPTION = ?, userUpdatedId = ?, storeId = ?, updatedAt = NOW() WHERE id = ?");
        $stmt->bind_param("ssiii", $name, $description, $userUpdatedId, $storeId, $id);

        if ($stmt->execute()) {
            $alert = '<div class="alert alert-primary" role="alert">Rol actualizado correctamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el rol.</div>';
        }

        $stmt->close();
    }

    return $alert;
}

function deleteRole($id)
{
    if (is_numeric($id)) {
        $stmt = MYSQLI->prepare("DELETE FROM ROLES WHERE ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    return false;
}

// ESTO LO USAMOS EN LA CREACION Y EDICION DEL PROVEEDOR
function getStores()
{
    $query = "SELECT * FROM STORES ORDER BY NAME ASC";
    $result = mysqli_query(MYSQLI, $query);

    if (!$result) {
        die("Query execution error: " . mysqli_error(MYSQLI));
    }

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_free_result($result);


    return $rows;
}

// ESTO LO USAMOS EN LA CREACION Y EDICION DEL PROVEEDOR
function getBranches()
{
    $query = "SELECT * FROM BRANCHES";
    $result = mysqli_query(MYSQLI, $query);

    if (!$result) {
        die("Query execution error: " . mysqli_error(MYSQLI));
    }
    $branches = [];
    while ($role = mysqli_fetch_assoc($result)) {
        $branches[] = $role;
    }
    mysqli_free_result($result);
    return $branches;
}
