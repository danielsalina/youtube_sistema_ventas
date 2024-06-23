<?php
// logout.php
session_start();
session_unset();
session_destroy();
header("Location: ../../index.php?page=login");
exit();
/* http: //localhost/Project/index.php?page=login
http://localhost/Project/app/views/index.php?page=login */