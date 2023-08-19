<?php

require 'includes/app.php';

if (!is_authenticate()) {
  header("Location: index.php");
  return;
}

use Contacts\Models\Contact;
use Contacts\Config\Database;

$contacts = Contact::find_all();

// $action_code = isset($_GET['action']) ? $_GET['action'] : null;

include_template('header');

?>
<h1 class="text-center mt-2">Contacts</h1>

<main class="section container">

  <?php if (isset($_SESSION["flash"])) : ?>
    <div class="center-alert">
      <div class="alert success">
        <p><?= $_SESSION["flash"]["message"] ?></p>
      </div>
    </div>
  <?php unset($_SESSION["flash"]); ?>
  <?php endif ?>
  
  <?php if (count($contacts) == 0) { ?>
    <div class="without-contacts text-center">
      <p>You have no contacts</p>
      <a href="create.php">Add one!</a>
    </div>
  <?php } ?>

  <div class="main-content">
    <?php if (count($contacts) > 0) { ?>
      <div class="align-right mb-3">
        <a class="btn btn-dark" href="create.php">Add Contact</a>
      </div>
    <?php } ?>

    <div class="flex-center">
      <div class="contacts">
        <?php foreach ($contacts as $contact) : ?>
          <div class="contact">
            <div class="contact-content">
              <div class="contact-img">
                <?php if (!$contact->get_image_name()) : ?>
                  <p class="no-image">No profile picture</p>
                <?php else : ?>
                  <img src="<?= 'images/' . $contact->get_image_name() ?>" alt="contact image">
                <?php endif; ?>
              </div>
              <div class="contact-details">
                <p class="contact-name"><?= $contact->get_name() . ' ' . $contact->get_paternal_last_name() . ' ' . $contact->get_maternal_last_name() ?></p>
                <p class="contact-phone-number"><?= $contact->get_phone_number() ?></p>
                <p class="contact-email"><?= $contact->get_email() ?></p>
                <p class="contact-category" style="margin: 1.8rem 0;">
                  <span>Cat:</span> <strong><?= $contact->get_category_name() ?></strong>
                </p>
              </div>
            </div>
            <div class="contact-actions">
              <a class="btn btn-blue" href="update.php?id=<?= $contact->get_id() ?>">Edit</a>
              <form action="delete.php" method="post">
                <input type="hidden" name="id" value="<?= $contact->get_id() ?>">
                <input type="submit" class="btn btn-red" value="Delete">
              </form>
            </div>
          </div>
        <?php endforeach ?>
      </div>
    </div>

  </div>

</main>
