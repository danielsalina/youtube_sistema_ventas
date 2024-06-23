<?php

require_once("../../config/db.php");

// Registrar cliente desde la creacion de la venta
if (isset($_POST["action"]) and $_POST["action"] == "client_register") {

    $alert = "";
    $dni = $_POST['client_dni'] ?? 1;
    $name = $_POST['client_name'] ?? "nombre_hardcode";
    $phoneNumber = $_POST['client_phone'] ?? 1;
    $address = $_POST['client_address'] ?? "direccion_hardcode";
    $email = $_POST['client_email'] ?? "correo_hardcode";
    $userCreatedId = $_SESSION["id_user"] ?? 9;
    $branchId = $_SESSION["branchId"];
    $storeId = $_SESSION['storeId'];
    /* $storeId = $_POST['storeId']; */

    $query = mysqli_query(MYSQLI, "SELECT * FROM CLIENTS WHERE EMAIL = '$email'");
    $result = mysqli_fetch_array($query);

    if ($result > 0) {
        $alert = '<div class="alert alert-danger" role="alert">El email ya existe.</div>';
    } else {
        $query_insert = mysqli_query(MYSQLI, "INSERT INTO CLIENTS (DNI, NAME, phoneNumber, ADDRESS, EMAIL, branchId, UserCreatedId, storeId) VALUES ($dni, '$name', '$phoneNumber', '$address', '$email', $branchId, $userCreatedId, $storeId)");

        if ($query_insert) {
            $alert = '<div class="alert alert-primary" role="alert">Cliente registrado satisfactoriamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al guardar el cliente.</div>';
        }
    }
}

// AJAX, Anular Venta
if (isset($_POST['action']) and $_POST['action'] == 'cancelSale') {

    session_start();

    $data = "";
    $token = md5($_SESSION['id_user']);
    $query_del = mysqli_query(MYSQLI, "DELETE FROM TEMPORARY_DETAILS WHERE tokenUser = '$token'");

    mysqli_close(MYSQLI);

    if ($query_del) {
        echo 'ok';
    } else {
        $data = 0;
    }

    exit;
}

// AJAX, Generar nueva venta
if (isset($_POST['action']) and $_POST['action'] == 'processSale') {

    session_start();

    $client_id = $_POST['clientId'];
    $token = md5($_SESSION['id_user']);
    $userCreatedId = $_SESSION['id_user'];
    $branchId = $_SESSION['branchId'];
    $storeId = $_SESSION['storeId'];
    /* $storeId = $_POST['storeId']; */

    $query = mysqli_query(MYSQLI, "SELECT * FROM TEMPORARY_DETAILS WHERE tokenUser = '$token' ");
    $result = mysqli_num_rows($query);

    if ($result > 0) {

        $total_with_discount = floatval(str_replace(',', '', $_POST["totalWithDiscount"]));
        $total_con_descuento_formateado = number_format($total_with_discount, 2, '.', '');
        $query_procesar = mysqli_query(MYSQLI, "CALL sp_process_sale($userCreatedId, $client_id, '$token', $total_con_descuento_formateado, $branchId, $storeId)");
        $result_detalle = mysqli_num_rows($query_procesar);

        if ($result_detalle > 0) {
            $data = mysqli_fetch_assoc($query_procesar);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
    mysqli_close(MYSQLI);
    exit;
}

function getSales(): array
{
    $query = "SELECT * FROM INVOICES ORDER BY DATE DESC";
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

function getSalesForDate(string $from, string $to): array
{
    $query = "SELECT * FROM INVOICES WHERE DATE(DATE) BETWEEN '$from' AND '$to'";

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
