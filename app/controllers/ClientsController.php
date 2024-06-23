<?php

session_start();

if (!isset($_SESSION['loggedin']) && $_GET['page'] != 'login') {
    header("Location: ../../index.php?page=login");
    exit();
}

include_once("../views/header.php");
include_once("../views/nav.php");
require_once("../models/ClientModel.php");

$alert = '';
$data_cliente = [];

// MOSTRAMOS LA VISTA PARA DAR DE ALTA UN NUEVO CLIENTE
if (isset($_REQUEST["name"]) && $_REQUEST["name"] === "client_new" && !isset($_POST["add_client"])) {
    $branches = getBranches();
    $stores = getStores();
    require_once("../views/clients/clientRegister.php");
}

// RECIBIMOS EL POST Y CREAMOS UN NUEVO CLIENTE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_client"])) {
    $branches = getBranches();
    $stores = getStores();
    $alert = addClient();
    require_once("../views/clients/clientRegister.php");
}

// Mostramos la lista de clientes
if (isset($_REQUEST["name"]) && $_REQUEST["name"] === "clients_list") {
    require_once("../views/clients/clientsList.php");
}

// Editamos el cliente
if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"]) && !isset($_POST["edit_client"])) {
    $branches = getBranches();
    $stores = getStores();
    $data_cliente = getClientById($_REQUEST["id"]);
    require_once("../views/clients/clientEdit.php");
}

// ACTUALIZAMOS EL CLIENTE EDITADO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_client"])) {
    $branches = getBranches();
    $stores = getStores();
    $alert = editClient();
    $data_cliente = getClientById($_POST["id"]);
    require_once("../views/clients/clientEdit.php");
}

// ELIMINAMOS UN CLIENTE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteClient($_GET['delete']);
    require_once("../views/clients/clientsList.php");
}

include_once("../views/footer.php");
