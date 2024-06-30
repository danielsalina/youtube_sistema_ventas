<?php

require_once(__DIR__ . "/../../config/db.php");

// AJAX, Buscar datos del producto con codigo
if (isset($_POST['action']) and $_POST['action'] == 'productInfowihtCode') {

    session_start();

    $data = "";
    $producto_id = $_POST['product'];
    $query = mysqli_query(MYSQLI, "SELECT * FROM products WHERE ID = $producto_id");

    mysqli_close(MYSQLI);

    $result = mysqli_num_rows($query);

    if ($result > 0) {

        $data = mysqli_fetch_assoc($query);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        exit;
    } else {
        $data = 0;
    }
}

// AJAX, Buscar datos del producto con nombre
if (isset($_POST['action']) && $_POST['action'] == 'productInfowihtName') {
    session_start();

    $data = "";
    $producto_name = $_POST['product'];

    // Escapar la entrada para evitar inyecciones SQL
    $producto_name = mysqli_real_escape_string(MYSQLI, $producto_name);

    // Consulta corregida
    $query = mysqli_query(MYSQLI, "SELECT * FROM products WHERE name LIKE '%$producto_name%'");

    // Verificar si la consulta fue exitosa
    if ($query) {
        $result = mysqli_num_rows($query);

        if ($result > 0) {
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(0);
        }
    } else {
        // Manejo de errores en la consulta
        echo json_encode(['error' => 'Error en la consulta a la base de datos.']);
    }

    mysqli_close(MYSQLI);
    exit;
}

// AJAX, Agregar producto a detalle temporal
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

// AJAX, Eliminar product del listado temporal
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
                   <a href="#" class="btn btn-danger text-center" onclick="event.preventDefault(); deleteProductDetail(' . $data['id'] . ');"><i class="fa fa-trash-o"></i> Eliminar Producto</a>
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
                  <a href="saleRegister.php" class="btn btn-success mx-2 px-4" id="apply3PercentDiscount" onclick="event.preventDefault(); apply3PercentDiscount(' . $total . ');">3%</a>
                  <a href="saleRegister.php" class="btn btn-success mx-2 px-4" id="apply5PercentDiscount" onclick="event.preventDefault(); apply5PercentDiscount(' . $total . ');">5%</a>
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

function getProviders()
{
    $query = "SELECT * FROM suppliers ORDER BY NAME ASC";

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

function addProduct()
{
    $alert = "";

    if (empty($_POST['supplier']) || empty($_POST['name']) || empty($_POST['price']) || $_POST['price'] <  0 || empty($_POST['quantity'] || $_POST['quantity'] <  0)) {

        $alert = '<div class="alert alert-danger" role="alert"> Todo los campos son obligatorios </div>';
    } else {
        $supplier = $_POST['supplier'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $userCreatedId = $_SESSION['id_user'];
        $branchId = $_SESSION['branchId'];
        /* $storeId = $_SESSION['storeId']; */
        $storeId = $_POST['storeId'];


        $query_insert = mysqli_query(MYSQLI, "INSERT INTO products (SUPPLIER, NAME, PRICE, STOCK, branchId, userCreatedId, storeId) VALUES ('$supplier', '$name', '$price', '$quantity', $branchId, '$userCreatedId', $storeId)");

        if ($query_insert) {
            $alert = '<div class="alert alert-primary" role="alert"> Producto Registrado </div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert"> Error al registrar el producto </div>';
        }
    }

    return $alert;
}

function newPriceMassive()
{
    $porcentaje = isset($_POST["porcentaje"]) ? filter_var($_POST["porcentaje"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : "";
    $userUpdatedId = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : "";
    /* $storeId = $_SESSION['storeId']; */
    $storeId = $_POST['storeId'];

    if ($porcentaje !== false && $porcentaje > 0) {
        $query = "UPDATE products SET PRICE = PRICE * (1 + (? / 100)), userUpdatedId = ?, storeId = ?, updatedAt = NOW()";
        $stmt = mysqli_prepare(MYSQLI, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "dii", $porcentaje, $userUpdatedId, $storeId);
            $query_update = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($query_update) {
                $alert = "Se le aplicó un " . $porcentaje . "% a todos los productos satisfactoriamente.";
            } else {
                $alert = "Hubo un error al actualizar los productos: " . mysqli_error(MYSQLI);
            }
        } else {
            $alert = "Error en la preparación de la consulta.";
        }
    } else {
        $alert = "Por favor, introduce un porcentaje válido y mayor que cero.";
    }

    return $alert;
}

function getProducts()
{
    /* $query = "SELECT * FROM products"; */

    $query = "SELECT p.id, p.name, p.price, p.stock, pr.name AS supplier 
          FROM products p 
          INNER JOIN suppliers pr ON p.supplier = pr.id";

    $result = mysqli_query(MYSQLI, $query);

    if (!$result) {
        die("Query execution error: " . mysqli_error(MYSQLI));
    }

    $products = [];

    while ($product = mysqli_fetch_assoc($result)) {
        $products[] = $product;
    }

    mysqli_free_result($result);

    return $products;
}

function getProductById($product_id = null)
{
    if ($product_id === null) {
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['productId']) ? (int)$_POST['productId'] : 0);
    }

    $data_producto = [];
    if ($product_id > 0) {
        $stmt = MYSQLI->prepare("SELECT p.id AS productId, p.name AS nombre_producto, p.price AS precio_producto, p.stock AS product_quantity, pr.id AS id_proveedor, pr.name AS nombre_proveedor, pr.phoneNumber AS telefono_proveedor, pr.address AS direccion_proveedor FROM products p INNER JOIN suppliers pr ON p.supplier = pr.id WHERE p.id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result_producto = $stmt->get_result();
            if ($result_producto->num_rows > 0) {
                $data_producto = $result_producto->fetch_assoc();
            }
            $stmt->close();
        } else {
            error_log("Error preparing statement: " . MYSQLI->error);
        }
    } else {
        error_log("Invalid product ID.");
    }

    return $data_producto;
}

function editProduct()
{
    $alert = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_product"])) {
        $product_id = (int)$_POST['productId'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $quantity = $_POST["quantity"];
        $user_id = (int)$_POST["user_id"];
        $provider_id = (int)$_POST['provider_id'];
        /* $storeId = $_SESSION['storeId']; */
        $storeId = $_POST['storeId'];

        $stmt = MYSQLI->prepare("UPDATE products SET NAME = ?, SUPPLIER = ?, PRICE = ?, STOCK = ?, userUpdatedId = ?, storeId = ?, updatedAt = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("siidiii", $name, $provider_id, $price, $quantity, $user_id, $storeId, $product_id);
            if ($stmt->execute()) {
                $alert = '<div class="alert alert-primary" role="alert">Producto modificado correctamente</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al modificar el producto</div>';
            }
            $stmt->close();
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error preparando la consulta: ' . MYSQLI->error . '</div>';
        }
    }

    return $alert;
}

function updatedProduct()
{
    $alert = '';
    $nombre_proveedor = '';
    $porcentaje = isset($_POST["porcentaje"]) ? filter_var($_POST["porcentaje"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : "";
    $supplier = isset($_POST["aumentar_por_proveedor"]) ? (int)$_POST["aumentar_por_proveedor"] : "";

    if ($porcentaje !== false && $porcentaje > 0 && $supplier !== false && $supplier > 0) {
        $stmt = MYSQLI->prepare("SELECT * FROM products WHERE SUPPLIER = ?");
        $stmt->bind_param("i", $supplier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userUpdatedId = $_SESSION['id_user'];
            $storeId = $_SESSION['storeId'];
            $update_stmt = MYSQLI->prepare("UPDATE products SET PRICE = ?, userUpdatedId = ?, storeId = ?, updatedAt = NOW() WHERE ID = ?");

            while ($data = $result->fetch_assoc()) {
                $precio_actual = $data['price'];
                $aumento = ($precio_actual * $porcentaje) / 100;
                $nuevo_precio = $precio_actual + $aumento;
                $update_stmt->bind_param("diii", $nuevo_precio, $userUpdatedId, $storeId, $data['id']);
                $update_stmt->execute();
            }

            $stmt_nombre = MYSQLI->prepare("SELECT * FROM suppliers WHERE ID = ?");
            $stmt_nombre->bind_param("i", $supplier);
            $stmt_nombre->execute();
            $result_nombre = $stmt_nombre->get_result();

            if ($result_nombre->num_rows > 0) {
                $nombre_proveedor = $result_nombre->fetch_assoc()['name'];
            }

            $stmt_nombre->close();
        } else {
            $alert = "No se encontraron productos para el supplier especificado.";
        }
        $stmt->close();
    } else {
        $alert = "Por favor, elige un supplier y un porcentaje válidos.";
    }

    return [
        'nombre_proveedor' => $nombre_proveedor,
        'porcentaje' => $porcentaje,
        'alert' => $alert
    ];
}

function deleteProductById()
{
    $product_id = (int)$_REQUEST['id'];

    $stmt = MYSQLI->prepare("DELETE FROM products WHERE ID = ?");
    $alert = "";

    if ($stmt) {

        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $alert = "Producto eliminado exitosamente.";
        } else {
            $alert = "No se encontró el product o no se pudo eliminar.";
        }

        $stmt->close();
    } else {
        $alert = "Error al preparar la consulta.";
    }
    return $alert;
}

// ESTO LO USAMOS EN LA CREACION Y EDICION DEL PROVEEDOR
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

// ESTO LO USAMOS EN LA CREACION Y EDICION DEL PROVEEDOR
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
