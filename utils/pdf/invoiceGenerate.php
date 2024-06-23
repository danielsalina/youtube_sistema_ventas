<?php

session_start();

require_once "../../config/db.php";
require_once 'fpdf/fpdf.php';

$client_id = $_REQUEST['clientId'];
$numero_factura = $_REQUEST['invoiceId'];
$total_with_discount = $_REQUEST['totalWithDiscount'];

// Consultas utilizando prepared statements
$data_store = mysqli_prepare(MYSQLI, "SELECT * FROM STORES");
mysqli_stmt_execute($data_store);
$resultado_configuracion = mysqli_stmt_get_result($data_store);
$configuracion = mysqli_fetch_assoc($resultado_configuracion);

$stmt_ventas = mysqli_prepare(MYSQLI, "SELECT * FROM INVOICES WHERE id = ?");
mysqli_stmt_bind_param($stmt_ventas, "i", $numero_factura);
mysqli_stmt_execute($stmt_ventas);
$result_venta = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ventas));

$stmt_clientes = mysqli_prepare(MYSQLI, "SELECT * FROM CLIENTS WHERE ID = ?");
mysqli_stmt_bind_param($stmt_clientes, "i", $client_id);
mysqli_stmt_execute($stmt_clientes);
$result_cliente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_clientes));

$query = "SELECT d.invoiceNumber, d.productId, d.quantity, p.id, p.name, p.price FROM INVOICE_DETAILS d INNER JOIN PRODUCTS p ON d.invoiceNumber = ? WHERE d.productId = p.id";
$stmt_productos = mysqli_prepare(MYSQLI, $query);
mysqli_stmt_bind_param($stmt_productos, "i", $numero_factura);
mysqli_stmt_execute($stmt_productos);
$productos_result = mysqli_stmt_get_result($stmt_productos);

$pdf = new FPDF('P', 'mm', array(120, 200));
$pdf->AddPage();
$pdf->SetMargins(8, 0, 0);
$pdf->Image("./img/dani.png", 12, 12, 20, 20, 'PNG');
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(92, 5, "DANI CODE ", 0, 2, 'R');
$pdf->SetFont('Arial', '', 4);
$pdf->Cell(90, 5, "Street 123, Internet City  ", 0, 2, 'R');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(100, 5, "DESARROLLO WEB - JS - PHP ", 0, 2, 'R');
$pdf->Image("./img/whatsapp.png", 76, 27, 6, 6, 'PNG');
$pdf->Cell(90, 5, "00-1234.5678", 0, 2, 'R');
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 5, "FACTURA Nro: ", 0, 0, 'L');
$pdf->Cell(20, 5, $numero_factura, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 5, "Fecha y Hora: ", 0, 0, 'L');
$pdf->Cell(0, 5, $result_venta['date'], 0, 1, 'L');
$pdf->Cell(20, 5, "-----------------------------------------------------------------------------------------------------------------------", 0, 0, 'L');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(100, 5, "Datos del cliente", 0, 1, 'C');
$pdf->Cell(18, 5, mb_convert_encoding("Razón Social: ", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(5, 5, $result_cliente['name'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(13, 5, mb_convert_encoding("Dirección: ", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25, 5, $result_cliente['address'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(6, 5, "DNI: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25, 5, $result_cliente['dni'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(12, 5, mb_convert_encoding("Teléfono: ", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25, 5, $result_cliente['phoneNumber'], 0, 1, 'L');
$pdf->Cell(20, 5, "-----------------------------------------------------------------------------------------------------------------------", 0, 0, 'L');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(100, 5, "Detalle de Productos", 0, 1, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(65, 5, 'Nombre', 0, 0, 'L');
$pdf->Cell(12, 5, 'Cant', 0, 0, 'L');
$pdf->Cell(14, 5, 'Precio', 0, 0, 'L');
$pdf->Cell(0, 5, 'Total', 0, 1, 'L');
$pdf->SetFont('Arial', '', 5.9);

while ($row = mysqli_fetch_assoc($productos_result)) {
	$pdf->Cell(68, 5, strtoupper($row['name']), 0, 0, 'L');
	$pdf->Cell(8, 5, $row['quantity'], 0, 0, 'L');
	$pdf->Cell(14, 5, "$ " . number_format($row['price'], 2, '.', ','), 0, 0, 'L');
	$importe = number_format($row['quantity'] * $row['price'], 2, '.', ',');
	$pdf->Cell(15, 5, "$ " . $importe, 0, 1, 'L');
}

$pdf->Cell(0, 5, "--------------------------------------------------------------------------------------------------------------------------------------------------", 0, 0, 'L');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);

// Mostrar total
$total_formateado = number_format($result_venta['total'], 2, '.', ',');
$pdf->Cell(96, 5, 'Total: $ ' . $total_formateado, 0, 1, 'R');

// Mostrar total con descuento si aplica
if (!empty($total_with_discount) && $total_with_discount != "0" && $total_with_discount != 0) {
	$total_with_discount = str_replace(',', '', $total_with_discount);
	$total_con_descuento_formateado = number_format((float)$total_with_discount, 2, '.', ',');
	$pdf->Cell(96, 5, 'Total Con Descuento: $ ' . $total_con_descuento_formateado, 0, 1, 'R');
}

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(40, 5, "Atendido por: " . $_SESSION['name'], 0, 0, 'L');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(100, 5, "Gracias por su compra", 0, 1, 'C');

$pdf->Output("Factura$numero_factura.pdf", "I");

// Cerrar conexiones y liberar recursos
mysqli_stmt_close($data_store);
mysqli_stmt_close($stmt_ventas);
mysqli_stmt_close($stmt_clientes);
mysqli_stmt_close($stmt_productos);
