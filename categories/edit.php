<?php

require '../includes/app.php';

if (!is_authenticate()) {
  header("Location: ../index.php");
}

use Contacts\Models\Category;
use Contacts\Models\Contact;

$error = null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!filter_var($id, FILTER_VALIDATE_INT))
   die("ERROR: Invalid id param");

if ($id == 1)
   die("Error: Category with id 1 no editable!");

$category = Category::get_by_id($id);

if ($category == null)
   die("ERROR 404: Category Not Found");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $category->set_name($_POST['name']);

   $error = $category->getError('edit');
   if (!$error) {
      $category->update();
      $_SESSION["flash"] = ["message" => "Category updated successfully."];
      header('Location: categories.php');
      return;
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
      <p>Edit category</p>
   </div>
   <form class="categories-form" action="edit.php?id=<?= $id ?>" method="post">
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" value="<?= $category->get_name() ?>" autofocus>

      <div class="align-right">
         <input class="btn btn-blue" type="submit" value="Edit">
      </div>
   </form>
</section>
