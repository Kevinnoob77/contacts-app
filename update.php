<?php

require_once 'includes/app.php';

if (!is_authenticate()) {
  header("Location: index.php");
}

use Contacts\Models\Contact;
use Contacts\Models\Category;

$error = null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

$categories = Category::find_all();

if (!filter_var($id, FILTER_VALIDATE_INT)) {
  header("Location: home.php");
  return;
}

$contact = Contact::get_by_id($id);

if ($contact->get_user_id() !== $_SESSION["user_id"]) {
  http_response_code(404);
  echo "HTTP 403 UNAUTHORIZED";
  return;
}

if (!$contact) {
  $index_url = '<a href="home.php">Return</a>';
  die("Error 404: Contact not found with id " . $_GET['id'] . " " . $index_url);
}

$previous_image = $contact->get_image_name();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $contact->set_attributes($_POST, $_FILES['image']);

  $error = $contact->get_error();

  if (!$error) {
    if ($previous_image)
      unlink(IMAGES_PATH . $previous_image);

    if ($contact->get_image_name()) {

      // Generate a unique name and set the image name of contact object
      $unique_img_name = md5(uniqid((string) rand(), true)) . strrchr($contact->get_image_name(), '.');
      $contact->set_image_name($unique_img_name);

      // Upload the image
      move_uploaded_file($contact->get_image()['tmp_name'], IMAGES_PATH . $contact->get_image_name());
    }

    // update record (if no change the image, be will update with previous image: $contact->set_image_name($previous_ima))
    $updated_contact = $contact->update();
    if ($updated_contact) {
      $_SESSION["flash"] = ["message" => "Contact updated successfully."];
      header("Location: home.php");
      return;
    } else {
      die($updated_contact);
    }
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
    <p>Edit contact</p>
  </div>
  <form action="update.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
    <div class="form-field">
      <label for="name">First name:</label>
      <input type="text" name="name" id="name" placeholder="Contact name" value="<?= $contact->get_name() ?>">
    </div>

    <div class="form-field">
      <label for="last_name">Last name <span>(optional)</span>:</label>
      <input type="text" name="last_name" id="last_name" placeholder="Last name" value="<?= trim($contact->get_paternal_last_name() . ' ' . $contact->get_maternal_last_name()); ?>">
    </div>

    <div class="form-field">
      <label for="phone_number">Phone number:</label>
      <input type="tel" name="phone_number" id="phone_number" placeholder="Phone number" value="<?= $contact->get_phone_number() ?>">
    </div>

    <div class="form-field">
      <label for="email">Email <span>(optional)</span>:</label>
      <input type="email" name="email" id="email" placeholder="Email" value="<?= $contact->get_email() ?>">
    </div>

    <div class="form-field">
      <label class="select-label" for="category">Select the category:</label>
      <select name="category" id="category">
        <option disabled <?= $contact->get_category_id() === 0 ? 'selected' : '' ?>>Category</option>
        <?php foreach ($categories as $category) { ?>
          <option value="<?= $category->get_id() ?>" <?= $category->get_id() == $contact->get_category_id() ? 'selected' : '' ?>>
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
      <input type="submit" class="btn btn-blue" value="Update">
    </div>
  </form>
</section>