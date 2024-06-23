<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once("config.php");

try {

    if (!MYSQLI) {
        throw new Exception("Error starting conncection to database" . mysqli_connect_error());
    }

    if (!mysqli_real_connect(MYSQLI, HOST, USER, PASSWORD, DATABASE)) {
        throw new Exception("Error connecting to database " . mysqli_connect_error());
    }
} catch (\Throwable $th) {
    echo "There was an error in the query " . $e->getMessage();
}
