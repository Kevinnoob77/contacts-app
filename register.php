<?php

require 'includes/app.php';

if (is_authenticate()) {
   header("Location: home.php");
}

use Contacts\Models\User;

$user = new User('', '', '');

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $user->set_username(trim($_POST['name']));
   $user->set_email(trim($_POST['email']));
   $user->set_password($_POST['password']);

   $error = $user->get_error('register');

   if (!$error) {
      if (!$user->existing_user()) {
         $user->save();
         session_start();
         $_SESSION["username"] = $user->get_username();
         $_SESSION["email"] = $user->get_email();
         header("Location: home.php");
         return;
      }
      $error = User::USER_REGISTERED_ERROR;
   }
}

include_template('header');

?>

<main class="container">
   <div class="login-content">
      <h3>Register by filling in all the fields</h3>
      <p class="desc">Create Account</p>

      <form class="login-form" action="register.php" method="post">

         <div class="center-alert">
            <?php if ($error) { ?>
               <div class="alert error">
                  <p><?= $error ?></p>
               </div>
            <?php } ?>
         </div>

         <div class="field">
            <div class="align-right-text">
               <label for="name">Name:</label>
            </div>
            <input type="text" name="name" id="name" placeholder="Your name" value="<?= $user->get_username() ?>" autofocus>
         </div>

         <div class="field">
            <div class="align-right-text">
               <label for="email">Email:</label>
            </div>
            <input type="text" name="email" id="email" value="<?= $user->get_email() ?>" placeholder="Your email">
         </div>

         <div class="field">
            <div class="align-right-text">
               <label for="password">Password:</label>
            </div>
            <input type="password" name="password" id="password" placeholder="Your password" value="<?= $user->get_password() ?>" value="">
         </div>

         <div class="align-right">
            <!-- <a href="register.php"></a> -->
            <input class="btn btn-blue" type="submit" value="Register me">
         </div>
      </form>
   </div>
</main>
