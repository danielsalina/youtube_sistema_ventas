<?php

session_start();

if (!isset($_SESSION['loggedin']) && $_GET['page'] != 'login') {
    header("Location: ../../index.php?page=login");
    exit();
}

include_once("../views/header.php");
include_once("../views/nav.php");
require_once("../models/StoreModel.php");

$alert = '';
$data_store = [];

// MOSTRAMOS LA VISTA PARA DAR DE ALTA UNA NUEVA TIENDA
if (isset($_REQUEST["name"]) && $_REQUEST["name"] === "store_new" && !isset($_POST["add_store"])) {
    require_once("../views/stores/storeRegister.php");
}

// RECIBIMOS EL POST Y CREAMOS UNA NUEVA TIENDA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_store"])) {
    $alert = addStore();
    require_once("../views/stores/storeRegister.php");
}

// MOSTRAMOS LA LISTA DE TIENDAS
if (isset($_REQUEST["name"]) && $_REQUEST["name"] === "stores_list") {
    require_once("../views/stores/storesList.php");
}

// EDITAMOS LA TIENDA
if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"]) && !isset($_POST["edit_store"])) {
    $data_store = getStoreById($_REQUEST["id"]);
    require_once("../views/stores/storeEdit.php");
}

// ACTUALIZAMOS LA TIENDA EDITADA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_store"])) {
    $alert = editStore();
    $data_store = getStoreById($_POST["id"]);
    require_once("../views/stores/storeEdit.php");
}

// ELIMINAMOS UNA TIENDA
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteStore($_GET['delete']);
    require_once("../views/stores/storesList.php");
}

include_once("../views/footer.php");
