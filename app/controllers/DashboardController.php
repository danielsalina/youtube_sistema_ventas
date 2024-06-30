<?php

session_start();

if (!isset($_SESSION['loggedin']) && $_GET['page'] != 'login') {
    header("Location: ../../index.php?page=login");
    exit();
}

include_once(__DIR__ . "/../views/header.php");
include_once(__DIR__ . "/../views/nav.php");
require_once(__DIR__ . "/../models/DashboardModel.php");

$porcentaje = getDataCardOne()["porcentaje_diferencia_ventas"];
$total_ventas = getDataCardOne()["total_ventas"];
$total_presupuestos = getDataCardTwo()["total_presupuestos"];
$porcentaje_diferencia_presupuestos = getDataCardTwo()["porcentaje_diferencia_presupuestos"];
$total_recaudado_mes_actual = getDataCardAndGraphic()["total_recaudado_mes_actual"];
$porcentaje_diferencia_recaudado = getDataCardAndGraphic()["porcentaje_diferencia_recaudado"];
$valores_grafico = getDataGraphic();

require_once(__DIR__ . "/../views/dashboard.php");
include_once(__DIR__ . "/../views/footer.php");
