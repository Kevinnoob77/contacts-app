<?php

define("TEMPLATES_PATH", __DIR__ . '/templates/');
// Without __DIR__
// define("TEMPLATES_PATH", 'templates/');

function include_template($template) {
  // include 'templates/' .  $template . '.php';
  include TEMPLATES_PATH . $template . '.php';
}

$success_messages = [
  '1' => "created successfully",
  '2' => "updated successfully",
  '3' => "deleted successfully",
];

function get_success_message(string $action_code, string $model): string {
  global $success_messages;  
  return $model . ' ' . $success_messages[$action_code];
}

function debug($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}

function is_authenticate(): bool {
  session_start();
  return $_SESSION["username"] ?? false;
}
