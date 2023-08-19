<?php

if (!isset($_SESSION)) session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$login_pages = $uri == '/' || $uri == '/index.php' || $uri == '/register.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/styles.css">
  <script defer src="/assets/js/index.js"></script>
  <title>Contacts App</title>
</head>

<body class="<?= $login_pages ? 'login-page' : '' ?>">
  <header class="header">

    <div class="header-content">
      <nav class="nav">
        <div class="nav-sections">
          <a class="logo" href="<?= $login_pages ? 'index.php' : '/home.php' ?>">
            <img class="logo-img" src="/assets/img/Official PHP Logo.svg" alt="PHP Logo">
            <p>ContactsApp</p>
          </a>

          <?php if ($login_pages) { ?>
            <a class="nav-link" href="register.php">Register</a>
          <?php } else { ?>
            <a class="nav-link" href="/home.php">Home</a>
            <a class="nav-link" href="/categories/categories.php">Categories</a>
          <?php } ?>
        </div>
        <?php if (isset($_SESSION["user_name"])) : ?>
          <div class="nav-email-logout">
            <?php if (str_contains($uri, "categories")) : ?>
              <a href="../logout.php">Log out</a>
            <?php else : ?>
              <a href="logout.php">Log out</a>
            <?php endif; ?>
            <p class="email"><?= $_SESSION["email"] ?></p>
          </div>
        <?php endif ?>
      </nav>
    </div>

  </header>
