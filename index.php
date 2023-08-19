<?php

require 'includes/app.php';

if (is_authenticate()) {
   header("Location: home.php");
}

use Contacts\Models\User;

$user = new User('', '', '');

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $user->set_email(trim($_POST['email']));
   $user->set_password($_POST['password']);

   $error = $user->get_error();
   if (!$error) {
      session_start();
      $user->set_attributes();

      $_SESSION["id"] = $user->get_id();
      $_SESSION["username"] = $user->get_username();
      $_SESSION["email"] = $user->get_email();

      header("Location: home.php");
   }
}

include_template('header');

?>

<main class="container">
   <div class="login-content">
      <h3>Create and manage your contacts</h3>
      <p>Log in</p>
      <form class="login-form" action="index.php" method="post">

         <div class="center-alert">
            <?php if ($error) { ?>
               <div class="alert error">
                  <p><?= $error ?></p>
               </div>
            <?php } ?>
         </div>

         <div class="field">
            <div class="align-right-text">
               <label for="email">Email:</label>
            </div>
            <input type="email" name="email" id="email" placeholder="Your email" value="<?= $user->get_email() ?>">
         </div>
         <div class="field">
            <div class="align-right-text">
               <label for="password">Password:</label>
            </div>
            <input type="password" name="password" id="password" placeholder="Your password" value="<?= $user->get_password() ?>">
         </div>
         <div class="flex">
            <a href="register.php">Create an account</a>
            <input class="btn btn-blue" type="submit" value="Log in">
         </div>
      </form>
   </div>
</main>
