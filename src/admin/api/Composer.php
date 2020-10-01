<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
     
   require_once __DIR__."/../modules/File.php";
   
   print_r(array_map(function($val) {
      return File::real_path($val);
   }, $_POST["screenshot-path"]));
   print_r($_POST);
   print_r($_FILES);
?>