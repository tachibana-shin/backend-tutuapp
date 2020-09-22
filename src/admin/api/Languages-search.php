<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
 
 
   new class {
      public function __construct() {
         $languages = json_decode(file_get_contents(__DIR__."/languages-name.json"));
         
         $query = $_GET["query"] ?? "";
         $result = [];
         foreach ( $languages as $value ) {
            if ( !empty(preg_match("/$query/", $value -> name)) || !empty(preg_match("/$query/", $value -> nativeName)) ) {
               array_push($result, $value);
            }
         }
         echo json_encode([
            "state" => [
               "error" => false,
               "code" => 200,
               "message" => ""
            ],
            "data" => $result
         ]);
      }
   }
   
?>