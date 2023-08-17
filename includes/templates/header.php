<?php
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $index_page = $uri == '/' || $uri == '/index.php';
  $login_or_resgister_page = $index_page || $uri == '/register.php' || $uri == '/login.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/styles.css">
  <script defer src="/assets/js/index.js"></script>
  <title>Contacts App</title>
</head>

<body class="<?= $index_page ? 'login-page' : '' ?>">
  <header class="header">

    <div class="header-content">
      <nav class="nav">
        <a class="logo" href="<?= $login_or_resgister_page ? 'index.php' : '/home.php' ?>">
          <img class="logo-img" src="/assets/img/Official PHP Logo.svg" alt="PHP Logo">
          <p>ContactsApp</p>
        </a>

        <?php if ($login_or_resgister_page) { ?>
          <a class="nav-link" href="register.php">Register</a>
          <a class="nav-link" href="login.php">Login</a>
        <?php } else { ?>
          <a class="nav-link" href="/home.php">Home</a>
          <a class="nav-link" href="/categories/categories.php">Categories</a>
        <?php } ?>
      </nav>
    </div>

  </header>
