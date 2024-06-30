<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="public/css/bootstrap.min.css" rel="stylesheet" />
  <link href="public/css/style.css" rel="stylesheet" />
  <link href="public/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
</head>

<body>
  <?php
  /* <?= __DIR__ . "/../app/controllers/LoginController.php" ?> */
  ?>

  <body class="gray-bg">
    <div class="container w-25 border border-info border-2 rounded animated fadeInDown mt-5 pt-5">
      <h3 class="text-center">Login</h3>
      <form action="./app/controllers/LoginController.php" method="POST" class="mt-5 p-5">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Correo</label>
          <input type="email" class="form-control" name="email" id="exampleInputEmail1 email" aria-describedby="emailHelp" placeholder="user@gmail.com" autofocus>
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Contraseña</label>
          <input type="password" class="form-control custom-placeholder" name="password" id="exampleInputPassword1 password" placeholder="123...">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        </div>
        <button type="submit" name="login" value="login" class="btn btn-primary w-100 my-2">Login</button>
      </form>
    </div>
    <div class="footer mx-auto text-center">
      <span>
        <a href="https://www.linkedin.com/in/danielsalina/" target="_blank" rel="noopener noreferrer"><i class="fa fa-linkedin-square" aria-hidden="true"> </i></a>
        <a href="https://github.com/danielsalina" target="_blank" rel="noopener noreferrer"><i class="fa fa-github" aria-hidden="true"> </i></a>
        <a href="https://www.instagram.com/soydani_code/" target="_blank" rel="noopener noreferrer"><i class="fa fa-instagram" aria-hidden="true"> </i></a>
      </span>
      <div><strong>Dani Code</strong></div>
    </div>
  </body>
</body>

</html>