<?php

date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once(__DIR__ . "/./config.php");

try {
  if (!MYSQLI) {
    throw new Exception("Ha ocurrido un error al intentar iniciar la conexión" . mysqli_connect_error());
  }

  if (!mysqli_real_connect(MYSQLI, HOST, USER, PASSWORD, DATABASE)) {
    throw new Exception("Error conectando a la base de datos" . mysqli_connect_error());
  }
} catch (\Throwable $th) {
  throw $th->getMessage();
}
