<?php

require '../includes/app.php';

if (!is_authenticate()) {
  header("Location: ../index.php");
}

use Contacts\Models\Category;
use Contacts\Models\Contact;

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
   die("Que haces la concha de tu madree!\n");

$id = $_POST['id'];

if (!filter_var($id, FILTER_VALIDATE_INT))
   die("Que haces la concha de tu madree!\n");

$category = Category::get_by_id(intval($id));
$categories = null;

if (!$category)
   die("Error 404 Category not found!\n");

$ha_contacst = false;

if (isset($_POST['destination_category_id'])) {
   Contact::change_category($id, intval($_POST['destination_category_id']));
   $category->delete();
   header("Location: categories.php?action=" . DELETE_SUCCESS_CODE);

} else if (!isset($_POST['destination_category_id']) && !Category::has_contacts($id)) {
   $category->delete();
   header("Location: categories.php?action=" . DELETE_SUCCESS_CODE);

} else {
   $has_contacst = true;
   $categories = Category::find_all();
}

include_template('header');

?>

<?php if ($has_contacst) { ?>
   <div id="modal" class="modal">
      <div class="modal-content">
         <h3>Contacts in "<?= $category->get_name() ?>" <span>Choose their new category</span></h2>
         <form action="delete.php" method="post">
            <select name="destination_category_id" id="">
               <?php foreach ($categories as $category) :
                  if ($category->get_id() !== intval($id)) : ?>
                     <option value="<?= $category->get_id() ?>"><?= $category->get_name() ?></option>
                  <?php endif;
               endforeach; ?>
            </select>
            <div class="contact-actions">
               <input type="hidden" name="id" value="<?= $id ?>">
               <a href="categories.php" class="btn btn-blue">Cancel</a>
               <button class="btn btn-red" type="submit">Delete</button>
            </div>
         </form>
      </div>
   </div>
<?php } ?>
