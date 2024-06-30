<?php

require_once(__DIR__ . "/../../config/db.php");
setlocale(LC_MONETARY, 'en_US');

// Registrar cliente desde la creacion del presupuesto
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

    $query = mysqli_query(MYSQLI, "SELECT * FROM clients WHERE EMAIL = '$email'");
    $result = mysqli_fetch_array($query);

    if ($result > 0) {
        $alert = '<div class="alert alert-danger" role="alert">El email ya existe.</div>';
    } else {
        $query_insert = mysqli_query(MYSQLI, "INSERT INTO clients (DNI, NAME, phoneNumber, ADDRESS, EMAIL, branchId, UserCreatedId, storeId) VALUES ($dni, '$name', '$phoneNumber', '$address', '$email', $branchId, $userCreatedId, $storeId)");

        if ($query_insert) {
            $alert = '<div class="alert alert-primary" role="alert">Cliente registrado satisfactoriamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al guardar el cliente.</div>';
        }
    }
}

// AJAX, Anular presupuesto
if (isset($_POST['action']) and $_POST['action'] == 'cancelEstimate') {

    session_start();

    $data = "";
    $token = md5($_SESSION['id_user']);
    $query_del = mysqli_query(MYSQLI, "DELETE FROM temporary_details WHERE tokenUser = '$token'");

    mysqli_close(MYSQLI);

    if ($query_del) {
        echo 'ok';
    } else {
        $data = 0;
    }

    exit;
}

// Eliminar product del listado temporal
if (isset($_POST['action']) and $_POST['action'] == 'eliminarProducto') {

    session_start();

    if (empty($_POST['id_detalle'])) {
        echo 'error';
    } else {
        $id_detalle = $_POST['id_detalle'];
        $token = md5($_SESSION['id_user']);
        $query_iva = mysqli_query(MYSQLI, "SELECT * FROM stores");
        $result_iva = mysqli_num_rows($query_iva);
        $query_detalle_tmp = mysqli_query(MYSQLI, "CALL sp_delete_temporal_detail($id_detalle,'$token')");
        $result = mysqli_num_rows($query_detalle_tmp);
        $detalleTabla = '';
        $sub_total = 0;
        $iva = 0;
        $total = 0;
        $data = "";
        $arrayDatadata = array();

        if ($result > 0) {

            if ($result_iva > 0) {
                $info_iva = mysqli_fetch_assoc($query_iva);
                $iva = $info_iva['iva'];
            }

            while ($data = mysqli_fetch_assoc($query_detalle_tmp)) {
                $precioTotal = round($data['quantity'] * $data['sellingPrice'], 2);
                $sub_total = round($sub_total + $precioTotal, 2);
                $total = round($total + $precioTotal, 2);

                $detalleTabla .= '<tr>
            <td>' . $data['productId'] . '</td>
            <td colspan="2">' . $data['name'] . '</td>
            <td>' . $data['quantity'] . '</td>
            <td>$ ' . number_format($data['sellingPrice'], 2) . '</td>
            <td>$ ' . number_format($precioTotal, 2) . '</td>
            <td class="text-center">
                <a href="#" class="btn btn-danger" onclick="event.preventDefault(); deleteEstimateDetail(' . $data['id'] . ');"><i class="fa fa-trash-o"></i>Eliminar Producto</a>
            </td>
            </tr>';
            }

            $impuesto = round(($sub_total * $iva) / 100, 2);
            $tl_sniva = round($sub_total - $impuesto, 2);
            $total = round($tl_sniva + $impuesto, 2);
            $detalleTotales = '<tr>
                <td colspan="5"><b>Sub_Total</b></td>
                <td><b>$ ' . number_format($tl_sniva, 2) . '</b></td>
                </tr>
                <tr>
                    <td colspan="5"><b>IVA (' . $iva . ')</b></td>
                    <td><b>$ ' . number_format($impuesto, 2) . '</b></td>
                </tr>
                <tr>
                    <td colspan="5"><b>Total</b></td>
                    <td><b>$ ' . number_format($total, 2) . '</b></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-left" id="text_total_con_descuento" style="display: none;"><b>Total Con Descuento</b></td>
                    <td colspan="2" class="text-left pl-1"><b><span style="display:none;" id="span_total_con_descuento">$</span> <input type="text" id="totalWithDiscount" value=0 style="border:none; readonly; display:none"></b></input></td>
                    <td colspan="5" class="text-right">
                      <a href="newSimate.php" class="btn btn-success mx-2 px-4" id="apply3PercentDiscount" onclick="event.preventDefault(); apply3PercentDiscount(' . $total . ');">3%</a>
                      <a href="newSimate.php" class="btn btn-success mx-2 px-4" id="apply5PercentDiscount" onclick="event.preventDefault(); apply5PercentDiscount(' . $total . ');">5%</a>
                    </td>
                </tr>';

            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
        } else {
            $data = 0;
        }
        mysqli_close(MYSQLI);
    }
    exit;
}

// Generar nuevo presupuesto
if (isset($_POST['action']) and $_POST['action'] == 'procesarPresupuesto') {

    session_start();

    $client_id = $_POST['clientId'];
    $token = md5($_SESSION['id_user']);
    $userCreatedId = $_SESSION['id_user'];
    $branchId = $_SESSION['branchId'];
    $storeId = $_SESSION['storeId'];
    /* $storeId = $_POST['storeId']; */

    $query = mysqli_query(MYSQLI, "SELECT * FROM temporary_details WHERE tokenUser = '$token' ");
    $result = mysqli_num_rows($query);

    if ($result > 0) {

        $total_with_discount = floatval(str_replace(',', '', $_POST["totalWithDiscount"]));
        $total_con_descuento_formateado = number_format($total_with_discount, 2, '.', '');
        $query_procesar = mysqli_query(MYSQLI, "CALL sp_process_budget($userCreatedId, $client_id, '$token', $total_con_descuento_formateado, $branchId, $storeId)");
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

// AJAX, agregar producto a detalle temporal     
if (isset($_POST['action']) && $_POST['action'] == 'addProductToDetail') {
    session_start();

    if (empty($_POST['product']) || empty($_POST['quantity'])) {
        echo 'error';
    } else {
        $product = $_POST['product'];
        $quantity = $_POST['quantity'];
        $tokenUser = md5($_SESSION['id_user']);
        $query_iva = mysqli_query(MYSQLI, "SELECT * FROM stores");
        $result_iva = mysqli_num_rows($query_iva);

        if (preg_match('/^\d+$/', $product)) { // When the value is numeric
            $codproducto = intval($product);
            $query_detalle_temp = mysqli_query(MYSQLI, "CALL sp_add_temporal_code_detail($codproducto, $quantity, '$tokenUser')");
        } else { // When the value is not numeric
            $productName = mysqli_real_escape_string(MYSQLI, $product);
            $stmt = MYSQLI->prepare("CALL sp_add_temporal_name_detail(?, ?, ?)");
            $stmt->bind_param("sis", $productName, $quantity, $tokenUser);
            $stmt->execute();
            $query_detalle_temp = $stmt->get_result();
            $stmt->close();
        }

        $result = mysqli_num_rows($query_detalle_temp);
        $detalleTabla = '';
        $sub_total = 0;
        $iva = 0;
        $total = 0;
        $arrayData = array();

        if ($result > 0) {
            if ($result_iva > 0) {
                $info_iva = mysqli_fetch_assoc($query_iva);
                $iva = $info_iva['iva'];
            }

            while ($data = mysqli_fetch_assoc($query_detalle_temp)) {
                $precioTotal = round($data['quantity'] * $data['sellingPrice'], 2);
                $sub_total = round($sub_total + $precioTotal, 2);
                $total = round($total + $precioTotal, 2);
                $detalleTabla .= '<tr>
                    <td>' . $data['productId'] . '</td>
                    <td colspan="2">' . $data['name'] . '</td>
                    <td>' . $data['quantity'] . '</td>
                    <td>$ ' . number_format($data['sellingPrice'], 2) . '</td>
                    <td>$ ' . number_format($precioTotal, 2) . '</td>
                    <td class="text-center">
                        <a href="#" class="btn btn-danger" onclick="event.preventDefault(); deleteProductDetail(' . $data['id'] . ');"><i class="fa fa-trash-o"></i> Eliminar Producto</a>
                    </td>
                </tr>';
            }

            $impuesto = round(($sub_total * $iva) / 100, 2);
            $tl_sniva = round($sub_total - $impuesto, 2);
            $total = round($tl_sniva + $impuesto, 2);
            $detalleTotales = '<tr>
                <td colspan="5"><b>Sub_Total</b></td>
                <td><b>$ ' . number_format($tl_sniva, 2) . '</b></td>
            </tr>
            <tr>
                <td colspan="5"><b>IVA (' . $iva . '%)</b></td>
                <td><b>$ ' . number_format($impuesto, 2) . '</b></td>
            </tr>
            <tr>
                <td colspan="5"><b>Total</b></td>
                <td><b>$ ' . number_format($total, 2) . '</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-left" id="text_total_con_descuento" style="display: none;"><b>Total Con Descuento</b></td>
                <td colspan="2" class="text-left pl-1"><b><span style="display:none;" id="span_total_con_descuento">$</span> <input type="text" id="totalWithDiscount" value=0 style="border:none; readonly; display:none"></b></input></td>
                <td colspan="5" class="text-right">
                    <a href="saleRegister.php" class="btn btn-success mx-2 px-4" id="apply3PercentDiscount" onclick="event.preventDefault(); apply3PercentDiscount(' . $total . ');">3%</a>
                    <a href="saleRegister.php" class="btn btn-success mx-2 px-4" id="apply5PercentDiscount" onclick="event.preventDefault(); apply5PercentDiscount(' . $total . ');">5%</a>
                </td>
            </tr>';

            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Error al obtener los detalles temporales.']);
        }
        mysqli_close(MYSQLI);
    }
    exit;
}

function getStimates(): array
{
    $query = "SELECT * FROM estimates ORDER BY DATE DESC";
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
