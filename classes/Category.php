<?php

namespace Contacts\Models;

use Contacts\Config\Database;
use PDO;

class Category
{
  private int $id;
  private string $created;

  const ERROR_INVALID_NAME = 'The name is required';

  public function __construct(private string $name) {
  }

  public function save($user_id) {
    $stmt = Database::get_instance()->prepare("INSERT INTO categories(user_id, name) VALUES($user_id, :name)");
    $stmt->execute([':name' => $this->name]);
  }

  public function update() {
    $query = Database::get_instance()->prepare("UPDATE categories SET name = :name WHERE id = :id");
    $query->bindParam(":name", $this->name);
    $query->bindParam(":id", $this->id);
    $query->execute();
  }

  public static function find_all(): array {
    $categories = array();
    $stmt = Database::get_instance()->query("SELECT * FROM categories WHERE user_id = {$_SESSION["user_id"]} ORDER BY id");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      array_push($categories, self::create_from_array($row));
    }
    
    return $categories;
  }

  public static function get_by_id(int $id) {
    $stmt = Database::get_instance()->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() == 0)
      return null;

    return self::create_from_array($stmt->fetch(PDO::FETCH_ASSOC));
  }

  public function delete(): void {
    Database::get_instance()->prepare("DELETE FROM categories WHERE id = :id")->execute([":id" => $this->id]);
  }

  public static function create_default_category($default_cat, $user_id): void {
    Database::get_instance()->query("INSERT INTO categories(name, created, user_id) VALUES('$default_cat', NOW(), $user_id)");
  }

  public function existing_category(): bool {
    $stmt = Database::get_instance()->prepare("SELECT * FROM categories WHERE user_id = :user_id AND LOWER(name) = LOWER(:name)");
    $stmt->execute([":name" => $this->name, ":user_id" => $_SESSION["user_id"]]);
    
    return $stmt->rowCount() > 0;
  }

  public function existing_category_edit(): bool {
    $stmt = Database::get_instance()->prepare("SELECT * FROM categories WHERE user_id = :user_id AND LOWER(name) = LOWER(:name) AND id != $this->id");
    $stmt->execute([":name" => $this->name, ":user_id" => $_SESSION["user_id"]]);
    
    return $stmt->rowCount() > 0;
  }

  public static function has_contacts(int $cat_id): bool {
    $query = Database::get_instance()->prepare("SELECT * FROM contacts WHERE category_id = :cat_id");
    $query->execute([":cat_id" => $cat_id]);

    if ($query->rowCount() == 0) {
      return false;
    }

    return true;
  }

  public static function create_from_array(array $data) : Category {
    $category = new Category($data['name']);
    $category->set_id($data['id']);
    $category->set_created($data['created']); 

    return $category;
  }

  public function getError($section = null): string|bool {
    if (strlen($this->name) == 0)
      return self::ERROR_INVALID_NAME;
    if ($section == 'edit') {
      return $this->existing_category_edit() ? "Category \"$this->name\" registered!" : false;
    }
    if ($this->existing_category())
      return "Category \"$this->name\" registered!";

    return false;
  }

  public function get_id(): int { return $this->id; }
  public function set_id(int $id) : void { $this->id = $id; }

  public function get_name(): string { return $this->name; }
  public function set_name(string $name): void { $this->name = trim($name); }

  public function get_created(): string { return $this->created; }
  public function set_created(string $created) : void { $this->created = $created; }
  
}
