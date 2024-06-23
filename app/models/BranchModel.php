<?php

require_once("../../config/db.php");

function addBranch()
{
    $alert = "";

    if (empty($_POST['name']) || empty($_POST['cuit'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } else {
        $cuit = $_POST['cuit'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $address = $_POST['address'];
        $userCreatedId = $_SESSION['id_user'];
        $storeId = $_POST['storeId'];

        // Revisar si el cuit ya existe
        $stmt = MYSQLI->prepare("SELECT * FROM BRANCHES WHERE CUIT = ?");
        $stmt->bind_param("s", $cuit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $alert = '<div class="alert alert-danger" role="alert">El cuit ya está registrado con otra sucursal.</div>';
        } else {
            // Insertar el nuevo rol
            $stmt_insert = MYSQLI->prepare("INSERT INTO BRANCHES (NAME, CUIT, EMAIL, phoneNumber, ADDRESS, userCreatedId, storeId) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssssii", $name, $cuit, $email, $phoneNumber, $address, $userCreatedId, $storeId);

            if ($stmt_insert->execute()) {
                $alert = '<div class="alert alert-primary" role="alert">Sucursal Registrada.</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al registrar la sucursal.</div>';
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
    return $alert;
}

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

function getBranchById($id)
{
    $stmt = MYSQLI->prepare("SELECT * FROM BRANCHES WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $branchId = $result->fetch_assoc();
    $stmt->close();
    return $branchId;
}

function editBranch()
{
    $alert = "";

    if (empty($_POST['name']) || empty($_POST['cuit']) || empty($_POST['email']) || empty($_POST['phoneNumber']) || empty($_POST['address'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } else {
        $id = $_POST['id'];
        $cuit = $_POST['cuit'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $address = $_POST['address'];
        $userUpdatedId = $_SESSION['id_user'];
        $storeId = $_POST['storeId'];

        $stmt = MYSQLI->prepare("UPDATE BRANCHES SET CUIT = ?, NAME = ?, EMAIL = ?, phoneNumber = ?, ADDRESS = ?, userUpdatedId = ?, storeId = ?, updatedAt = NOW() WHERE ID = ?");
        $stmt->bind_param("sssisiii", $cuit, $name, $email, $phoneNumber, $address, $userUpdatedId, $storeId, $id);

        if ($stmt->execute()) {
            $alert = '<div class="alert alert-primary" role="alert">Sucursal actualizada correctamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar la sucursal.</div>';
        }

        $stmt->close();
    }

    return $alert;
}

function deleteBranch($id)
{
    if (is_numeric($id)) {
        $stmt = MYSQLI->prepare("DELETE FROM BRANCHES WHERE ID = ?");
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
