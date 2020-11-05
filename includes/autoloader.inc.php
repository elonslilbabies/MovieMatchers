<?php
spl_autoload_register('myAutoLoader');

function myAutoLoader($className) {
  // include 'classes/' . str_replace('\\', "/", $className) . '.class.php';
  $path = 'classes/';
  $extension = '.class.php';
  $fullPath = $path . $className . $extension;
  if (!file_exists($fullPath)) {
    return false;
  }
  include_once $fullPath;
}

// function myAutoLoader($className) {
//   include 'classes/' . str_replace('\\', "/", $className) . '.class.php';
// }

 ?>
