<?php

include_once(__DIR__ . "/../../config/db.php");

// ACTUALIZAMOS LOS DATOS DE LA TIENDA
/* if (isset($_POST["edit_store"]) == "edit_store") {

    $alert = "";

    if (empty($_POST['name']) || empty($_POST['phoneNumber']) || empty($_POST['address'])) {
        $alert = '<p class"error">Todos los campos son requeridos</p>';
    } else {
        $store_id = $_POST['id'];
        $name = $_POST['name'];
        $phoneNumber = $_POST['phoneNumber'];
        $address = $_POST['address'];
        $usuario_id = $_SESSION['id_user'];
        $result = 0;

        $sql_update = mysqli_query(MYSQLI, "UPDATE STORE _CONFIGURATIONS SET NAME = '$name' , phoneNumber = '$phoneNumber', ADDRESS = '$address', userCreatedId = $userCreatedId WHERE ID = $store_id");

        if ($sql_update) {
            $alert = '<p class"exito">Tienda actualizada correctamente</p>';
        } else {
            $alert = '<p class"error">Error al actualizar la tienda</p>';
        }
    }
} */

function addStore()
{

    if (empty($_POST['phoneNumber']) || empty($_POST['address'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
    } else {
        $cuit_cuil = $_POST['cuit_cuil'];
        $name = $_POST['name'];
        $tradeName = $_POST['tradeName'];
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $iva = $_POST['iva'];
        /* $branchId = $_SESSION['branchId']; */
        $userCreatedId = $_SESSION['id_user'];

        // Consultar si el CUIT_CUIL ya está registrado
        $stmt = MYSQLI->prepare("SELECT * FROM stores WHERE CUIT_CUIL = ?");
        $stmt->bind_param("s", $cuit_cuil);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $alert = '<div class="alert alert-danger" role="alert">El CUIT_CUIL ya está registrado con otra tienda</div>';
        } else {
            // Insertar nueva tienda
            $stmt_insert = MYSQLI->prepare("INSERT INTO stores (CUIT_CUIL, NAME, tradeName, phoneNumber, email, ADDRESS, iva, userCreatedId) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssssssii", $cuit_cuil, $name, $tradeName, $phoneNumber, $email, $address, $iva, $userCreatedId);

            if ($stmt_insert->execute()) {
                $alert = '<div class="alert alert-primary" role="alert">Tienda registrada</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al registrar tienda</div>';
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }

    return $alert;
}

function getStores()
{
    $query = "SELECT * FROM stores ORDER BY NAME ASC";
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

function getStoreById($id_store)
{

    $stmt = MYSQLI->prepare("SELECT * FROM stores WHERE ID = ?");
    $stmt->bind_param("i", $id_store);
    $stmt->execute();
    $result_store = $stmt->get_result();

    if ($result_store->num_rows == 0) {
        $stmt->close();
        return null;
    }

    $data_store = $result_store->fetch_assoc();
    $stmt->close();

    return $data_store;
}

function editStore()
{
    $alert = "";

    if (empty($_POST['name']) || empty($_POST['phoneNumber']) || empty($_POST['address'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } else {
        $id = $_POST['id'];
        $cuit_cuil = $_POST['cuit_cuil'];
        $name = $_POST['name'];
        $tradeName     = $_POST['tradeName'];
        $phoneNumber = $_POST['phoneNumber'];
        $email     = $_POST['email'];
        $address = $_POST['address'];
        $iva     = $_POST['iva'];
        /* $branchId = $_SESSION['branchId']; */
        $userUpdatedId = $_SESSION['id_user'];

        $stmt = MYSQLI->prepare("UPDATE stores SET CUIT_CUIL = ?, NAME = ?, tradeName = ?, phoneNumber = ?, EMAIL = ?, ADDRESS = ?, IVA = ?, userUpdatedId = ?, updatedAt = NOW() WHERE ID = ?");
        $stmt->bind_param("sssissiii", $cuit_cuil, $name, $tradeName, $phoneNumber, $email, $address, $iva, $userUpdatedId, $id);


        if ($stmt->execute()) {
            $alert = '<div class="alert alert-primary" role="alert">Tienda actualizada correctamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar la tienda.</div>';
        }

        $stmt->close();
    }

    return $alert;
}

function deleteStore($id)
{
    if (is_numeric($id)) {
        $stmt = MYSQLI->prepare("DELETE FROM stores WHERE ID = ?");
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

function getBranchById($id)
{
    $stmt = MYSQLI->prepare("SELECT * FROM branches WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $branchId = $result->fetch_assoc();
    $stmt->close();
    return $branchId;
}

function getBranches()
{
    $query = "SELECT * FROM branches";
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
