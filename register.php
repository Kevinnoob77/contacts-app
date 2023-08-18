<?php

require 'includes/app.php';


include_template('header');

?>

<main class="container">
   <div class="login-content">
      <!-- <h3>Create new account, fill all the fields</h3> -->
      <h3>Register by filling in all the fields</h3>
      <p>Create Account</p>
      <form class="login-form" action="index.php" method="post">

         <div class="field">
            <div class="align-right-text">
               <label for="name">Name:</label>
            </div>
            <input type="text" name="name" id="name" placeholder="Your name" autofocus>
         </div>

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
            <input type="password" name="password" id="password" placeholder="Your password" value="">
         </div>

         <div class="align-right">
            <!-- <a href="register.php"></a> -->
            <input class="btn btn-blue" type="submit" value="Create Account">
         </div>
      </form>
   </div>
</main>
