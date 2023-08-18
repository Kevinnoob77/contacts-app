<?php

require 'includes/app.php';


include_template('header');

?>

<main class="container">
   <div class="login-content">
      <h3>Create and manage your contacts</h3>
      <p>Log in</p>
      <form class="login-form" action="index.php" method="post">
         <div class="field">
            <div class="align-right-text">
               <label for="email">Email:</label>
            </div>
            <input type="email" name="email" id="email" placeholder="Your email">
         </div>
         <div class="field">
            <div class="align-right-text">
               <label for="password">Password:</label>
            </div>
            <input type="text" name="password" id="password" placeholder="Your password">
         </div>
         <div class="flex">
            <a href="register.php">Create an account</a>
            <input class="btn btn-blue" type="submit" value="Log in">
         </div>
      </form>
   </div>
</main>
