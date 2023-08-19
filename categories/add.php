<?php

require '../includes/app.php';

if (!is_authenticate()) {
  header("Location: index.php");
}

use Contacts\Models\Category;
use Contacts\Models\Contact;

$error = null;
$name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $category = new Category($_POST['name']);
   $name = $category->get_name();

   $error = $category->getError();

   if (!$error) {
      $category->save();
      header('Location: categories.php?action=' . CREATE_SUCCESS_CODE);
   }
}

include_template('header');

?>

<div class="center-alert">
  <?php if ($error) { ?>
    <div class="alert error categ">
      <p><?= $error ?></p>
    </div>
  <?php } ?>
</div>

<section class="section cat-form-container">
   <div class="legend">
      <p>Add category</p>
   </div>
   <form class="categories-form" method="post">
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" autofocus>

      <div class="align-right">
         <input class="btn btn-blue" type="submit" value="Create">
      </div>
   </form>
</section>
