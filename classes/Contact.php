<?php

namespace Contacts\Models;

use Contacts\Config\Database;
use PDO;
use PDOException;

class Contact
{
  const MAX_FILE_SIZE = 1024 * 1024; // 1 megabyte
  const ERROR_NAME_REQUIRED = "The name is required";
  const ERROR_PHONE_NUMBER_REQUIRED = "The phone number is required";
  const ERROR_INVALID_PHONE_NUMBER = "Invalid phone number (9 - 12 digits)";
  const ERROR_INVALID_EMAIL = "Please enter a valid email";
  const ERROR_CATEGORY_REQUIRED = "Please select a category";
  const ERROR_IMAGE_TOO_LARGE = "The image is too large (max 1mb)";

  private int     $id;
  private string  $name;
  private ?string $paternal_last_name;
  private ?string $maternal_last_name;
  private string  $phone_number;
  private ?string $email;
  private array   $image; // Represents the image information received from the server with $_FILES
  private int     $category_id;
  private string  $category_name;

  public function __construct() {
    $this->name = '';
    $this->paternal_last_name = null;
    $this->phone_number = '';
    $this->email = null;
    $this->category_id = 0;
    $this->maternal_last_name = null;
  }

  public function save() {
    $stmt = self::get_connection()->prepare("INSERT INTO contacts(name, paternal_last_name, maternal_last_name, phone_number, email, category_id, image) VALUES (:name, :paternal_last_name, :maternal_last_name, :phone_number, :email, :category_id, :image)");

    $stmt->execute([
      ":name" => $this->name,
      ":paternal_last_name" => $this->paternal_last_name,
      ":maternal_last_name" => $this->maternal_last_name,
      ":phone_number" => $this->phone_number,
      ":email" => $this->email,
      ":category_id" => $this->category_id,
      ":image" => $this->image['name'] ? $this->image['name'] : null,
    ]);
  }

  public function update(): string|bool{
    $conn = self::get_connection();
    try {
      $statement = $conn->prepare("UPDATE contacts SET
                                    name = :name,
                                    paternal_last_name = :paternal_last_name,
                                    maternal_last_name = :maternal_last_name,
                                    phone_number = :phone_number,
                                    email = :email,
                                    category_id = :category_id,
                                    image = :image
                                    WHERE id = $this->id");
      $statement->execute([
        ':name' => $this->name,
        ':paternal_last_name' => $this->paternal_last_name,
        ':maternal_last_name' => $this->maternal_last_name,
        ':phone_number' => $this->phone_number,
        ':email' => $this->email,
        ':category_id' => $this->category_id,
        ':image' => strlen($this->image['name']) > 0 ? $this->image['name'] : null,
      ]);
    } catch (PDOException $e) {
      return $e->getMessage();
    }

    return true;
  }

  public static function delete(int $id) {
    $stmt = self::get_connection()->prepare("DELETE FROM contacts WHERE id = :id"); 
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() == 0) {
      return false;
    }

    return true;
  }

  public static function find_all(): array {
    $contacts = array();
    $stmt = self::get_connection()->query("SELECT contacts.*, categories.name as category_name FROM contacts INNER JOIN categories ON contacts.category_id = categories.id");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $contact = self::create_from_array($row);
      $contact->category_name = $row['category_name'];
      array_push($contacts, $contact);
    }

    return $contacts;
  }

  public static function get_by_id(int $id): Contact|null {
    $stmt = Database::get_instance()->query("SELECT * FROM contacts WHERE id = $id");
    // $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() == 0)
      return null;

    return self::create_from_array($stmt->fetch(PDO::FETCH_ASSOC));
  }

  public static function change_category(int $category_id, int $new_category_id): void {
    $stmt = Database::get_instance()->prepare("UPDATE contacts SET category_id = :new_category_id WHERE category_id = :category_id");
    $stmt->execute([":new_category_id" => $new_category_id, ":category_id" => $category_id]);
  }

  /**
   * Set the attributes with form data `($_POST)`
   * 
   * @param array $data - $_POST array
   * @param array $image - array from $_FILES with the information of the file (image)
   */
  public function set_attributes(array $data, array $image): void {
    $this->name = trim($data['name']);
    $this->phone_number = trim($data['phone_number']);
    $this->email = strlen(trim($data['email'])) > 0 ? trim($data['email']) : null;
    $this->category_id = isset($data['category']) ? intval($data['category']) : 0;
    $this->image = $image;

    $last_name = explode(" ", trim($data['last_name']));

    if (count($last_name) == 1 && strlen($last_name[0]) > 0)
      $this->paternal_last_name = $last_name[0];
    else if (count($last_name) == 2) {
      $this->set_paternal_last_name($last_name[0]);
      $this->set_maternal_last_name($last_name[1]);
    }
  }

  private static function create_from_array(array $arr): Contact {
    $contact = new Contact();
    $contact->set_id($arr['id']);
    $contact->set_name($arr['name']);
    $contact->set_paternal_last_name($arr['paternal_last_name']);
    $contact->set_phone_number($arr['phone_number']);
    $contact->set_email($arr['email']);
    $contact->set_maternal_last_name($arr['maternal_last_name']);
    $contact->image['name'] = $arr['image']; // Solo se agrega el nombre al array image porque en el parametro $arr viene como un string (el nombre)
    $contact->category_id = $arr['category_id'];

    return $contact;
  }

  private static function get_connection() {
    return Database::get_instance();
  }
  
  /**
   * @return string - const of error or false if there is no error
   */
  public function get_error(): string|bool {
    if (!$this->name)
      return self::ERROR_NAME_REQUIRED;
    if (!$this->phone_number)
      return self::ERROR_PHONE_NUMBER_REQUIRED;
    if (
      !filter_var($this->phone_number, FILTER_VALIDATE_INT) ||
      strlen($this->phone_number) < 9 ||
      strlen($this->phone_number) > 12
    )
      return self::ERROR_INVALID_PHONE_NUMBER;
    if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL))
      return self::ERROR_INVALID_EMAIL; 
    if ($this->category_id == 0)
      return self::ERROR_CATEGORY_REQUIRED;
    if (isset($this->image['size']) && $this->image['size'] > self::MAX_FILE_SIZE)
      return self::ERROR_IMAGE_TOO_LARGE;

    return false;
  }

  public function get_id(): int { return $this->id; }
  public function set_id(int $id) { $this->id = $id; }

  public function get_name(): string { return $this->name; }
  public function set_name(string $name): void { $this->name = $name; }

  public function get_paternal_last_name(): string|null { return $this->paternal_last_name; }
  public function set_paternal_last_name(string|null $value) { $this->paternal_last_name = $value; }

  public function get_maternal_last_name(): string|null { return $this->maternal_last_name; }
  public function set_maternal_last_name(string|null $value) { $this->maternal_last_name = $value; }

  public function get_phone_number(): string { return $this->phone_number; }
  public function set_phone_number(string $phone) { $this->phone_number = $phone; }

  public function get_email(): ?string { return $this->email; }
  public function set_email(?string $email) { $this->email = $email; }

  public function get_image(): array { return $this->image; }

  public function get_image_name(): ?string {
    return isset($this->image['name']) && $this->image['name'] ? $this->image['name'] : null;
  }

  public function set_image_name(string $imgname) { $this->image['name'] = $imgname; }

  public function get_category_id(): int { return $this->category_id; }
  public function set_category_id(int $cat_id) { $this->category_id = $cat_id; }
  
  public function get_category_name(): string { return $this->category_name; }

}
