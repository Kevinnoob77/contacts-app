<?php

namespace Contacts\Models;

use Contacts\Config\Database;
use PDO;

class User
{
   private int $_id;

   const NAME_REQUIRED_ERROR = "User name is required";
   const EMAIL_REQUIRED_ERROR = "Email is required";
   const INVALID_EMAIL_ERROR = "Please insert a valid email";
   const PASSWORD_REQUIRED_ERROR = "Password is required";
   const INVALID_PASSWORD_ERROR = "Invalid password <span>(min 6 chars, at least 2 letters and 2 numbers)</span>";
   const USER_REGISTERED_ERROR = "This email is already registered";
   const EMAIL_NO_REGISTERED_ERROR = "Email not registered";
   const INCORRECT_PASSWORD = "The password is incorrect";

   public function __construct(
      private string $_username,
      private string $_email,
      private ?string $_password
   ) {
   }

   public function save() {
      $stmt = Database::get_instance()
         ->prepare("INSERT INTO users(user_name, email, password) VALUES(:username, :email, :password)")
         ->execute([
            ':username' => $this->_username,
            ':email' => $this->_email,
            ':password' => password_hash($this->_password, PASSWORD_BCRYPT),
         ]);
   }

   public function existing_user(): ?User {
      $statement = Database::get_instance()->prepare("SELECT * FROM users WHERE email = :email");
      $statement->execute([":email" => $this->_email]);

      if ($statement->rowCount() == 0)
         return null;

      return self::create_from_array($statement->fetch(PDO::FETCH_ASSOC));
   }

   public function set_attributes(): void {
      debug($this->_email);
      $statement = Database::get_instance()->query("SELECT * FROM users WHERE email = '$this->_email'");
      $result = $statement->fetch(PDO::FETCH_ASSOC);

      $this->_id = $result['id'];
      $this->_username = $result['user_name'];
   }

   /**
    * @return string - error message or false if there is no error
    */
   public function get_error(string $section = null): string|bool {
      if ($section == 'register' && !$this->_username)
         return self::NAME_REQUIRED_ERROR;
      if (!$this->_email)
         return self::EMAIL_REQUIRED_ERROR;
      if (!filter_var($this->_email, FILTER_VALIDATE_EMAIL))
         return self::INVALID_EMAIL_ERROR;
      if (!$this->_password)
         return self::PASSWORD_REQUIRED_ERROR;
      if ($section == null) {
         $user = $this->existing_user();
         if (!$user) {
            return self::EMAIL_NO_REGISTERED_ERROR;
         } else if (!password_verify($this->_password, $user->get_password())) {
            return self::INCORRECT_PASSWORD;
         }
      }
      if (!self::validate_password($this->_password))
         return self::INVALID_PASSWORD_ERROR;
      
      return false;
   }

   public static function validate_password(string $pass): bool {
      return strlen($pass) >= 6 &&
            preg_match_all('/[a-zA-Z]/', $pass) >= 2 &&
            preg_match_all('/[0-9]/', $pass) >= 2;
   }

   public static function create_from_array(array $data): User {
      $user = new User($data['user_name'], $data['email'], $data['password']);
      $user->set_id($data['id']);

      return $user;
   }

   /**
    * Get the value of _id
    */
   public function get_id()
   {
      return $this->_id;
   }

   /**
    * Set the value of _id
    *
    * @return  self
    */
   public function set_id($_id)
   {
      $this->_id = $_id;

      return $this;
   }

   /**
    * Get the value of _username
    */
   public function get_username()
   {
      return $this->_username;
   }

   /**
    * Set the value of _username
    *
    * @return  self
    */
   public function set_username($_username)
   {
      $this->_username = $_username;

      return $this;
   }

   /**
    * Get the value of _email
    */
   public function get_email()
   {
      return $this->_email;
   }

   /**
    * Set the value of _email
    *
    * @return  self
    */
   public function set_email($_email)
   {
      $this->_email = $_email;

      return $this;
   }


   /**
    * Get the value of _password
    */
   public function get_password()
   {
      return $this->_password;
   }

   /**
    * Set the value of _password
    *
    * @return  self
    */
   public function set_password($_password)
   {
      $this->_password = $_password;

      return $this;
   }
}
