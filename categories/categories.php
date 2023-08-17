<?php

require_once __DIR__ .  '/../includes/app.php';

use Contacts\Models\Category;

$categories = Category::find_all();

$action_code = isset($_GET['action']) ? $_GET['action'] : null;

include_template('header');
?>

<section class="container section">
  <h2 class="mb-3 text-center">Categories</h2>

  <?php if (filter_var($action_code, FILTER_VALIDATE_INT) && isset($success_messages[$action_code])) { ?>
    <div class="center-alert">
      <div class="alert success categ">
        <p><?= get_success_message($action_code, 'Category') ?></p>
      </div>
    </div>
  <?php } ?>

  <div class="categories-content">
    <div class="align-light mb-3">
      <a class="btn btn-dark" href="add.php">Add Category</a>
    </div>

    <div class="categories-container">
      <?php foreach ($categories as $category) : ?>
        <div class="category">
          <h3 class="category-name"><?= $category->get_name() ?> </h3>
          <p class="category-date">Creation date: <?= $category->get_created() ?></p>
          <div class="category-btns">
            <?php if ($category->get_id() == 1) :  ?>
              <button class="btn inactive">Edit</button>
            <?php else : ?>
              <a href="edit.php?id=<?= $category->get_id() ?>" class="btn btn-blue">Edit</a>
            <?php endif ?>
            <form action="delete.php" method="post">
              <input type="hidden" name="id" value="<?= $category->get_id() ?>">
              <button type="submit" class="btn btn-red <?= $category->get_id() == 1 ? 'inactive' : '' ?>" id="delete-btn">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
