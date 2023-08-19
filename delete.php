<?php

require 'includes/app.php';

if (!is_authenticate()) {
   header("Location: index.php");
   return;
}

use Contacts\Config\Database;
use Contacts\Models\Contact;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : null;

   $stmt = Database::get_instance()->query("SELECT * FROM contacts WHERE id = $id");
   $r = $stmt->fetch();

   if (Contact::delete($id)) {
      $_SESSION["flash"] = ["message" => "Contact removed successfully."];
      header("Location: home.php");
      return;
   }
   die("Error: Contact not found!" . PHP_EOL);
}
