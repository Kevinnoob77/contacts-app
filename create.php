<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Create a new Contact

require 'includes/app.php';

use Contacts\Models\Category;
use Contacts\Models\Contact;

$categories = Category::find_all();
$contact = new Contact();

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $image = $_FILES['image'];

  $contact->set_attributes($_POST, $image);

  $error = $contact->get_error();

  if (!$error) {
    if ($contact->get_image_name()) {
      // Create the images directory
      if (!is_dir(IMAGES_PATH)) { 
        mkdir(IMAGES_PATH);
      }
      // Generate a unique name and set the image name of contact object
      $unique_img_name = md5(uniqid((string) rand(), true)) . strrchr($image['name'], '.');
      $contact->set_image_name($unique_img_name);

      // Upload the image
      move_uploaded_file($contact->get_image()['tmp_name'], IMAGES_PATH . $contact->get_image_name());
    }

    $contact->save(); // Insert into database
    header("Location: home.php?action=" . CREATE_SUCCESS_CODE);
  }
}

include_template('header');

?>

<div class="center-alert">
  <?php if ($error) { ?>
    <div class="alert error absolute">
      <p><?= $error ?></p>
    </div>
  <?php } ?>
</div>

<section class="form-container">
    <div class="legend">
      <p>Add new contact</p>
    </div>
  <form action="create.php" method="post" enctype="multipart/form-data">
    <div class="form-field">
      <label for="name">First name:</label>
      <input
        type="text"
        name="name"
        id="name"
        placeholder="Contact name"
        value="<?= $contact->get_name() ?>"
      >
    </div>

    <div class="form-field">
      <label for="last_name">Last name <span>(optional)</span>:</label>
      <input 
        type="text"
        name="last_name"
        id="last_name"
        placeholder="Last name"
        value="<?= trim($contact->get_paternal_last_name() . ' ' . $contact->get_maternal_last_name()) ?>"
      >
    </div>

    <div class="form-field">
      <label for="phone_number">Phone number:</label>
      <input
        type="tel"
        name="phone_number"
        id="phone_number"
        placeholder="Phone number"
        value="<?= $contact->get_phone_number() ?>"
      >
    </div>
    
    <div class="form-field">
      <label for="email">Email <span>(optional)</span>:</label>
      <input
        type="email"
        name="email"
        id="email"
        placeholder="Email"
        value="<?= $contact->get_email()  ?>"
      >
    </div>

    <div class="form-field">
      <label class="select-label" for="category">Select the category:</label>
      <select name="category" id="category">
        <option disabled <?= $contact->get_category_id() === 0 ? 'selected' : '' ?> >Category</option>
        <?php foreach ($categories as $category) { ?>
          <option
              value="<?= $category->get_id() ?>"
              <?= $category->get_id() == $contact->get_category_id() ? 'selected' : '' ?>
            >
            <?= $category->get_name() ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="form-field">
      <label class="img-label" for="image">Imagen:</label>
      <input type="file" id="image" accept="image/jpeg, image/png" name="image">
    </div>

    <div class="align-right">
      <input type="submit" class="btn btn-blue" value="Create Contact">
    </div>
  </form>
</section>
