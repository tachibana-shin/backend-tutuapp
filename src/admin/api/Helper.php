<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   
   require_once __DIR__."/../modules/ErrorMS.php";
   
   new class {
       public function __construct() {
           switch ( $_SERVER["REQUEST_METHOD"] ) {
               case "GET":
                   $this -> read();
           }
       }
       
       private function read() {
           if ( $_GET["lang"] ?? false ) {
               $lang = $_GET["lang"];
               
               if ( !is_file(__DIR__."/../helpers/$lang.json") ) {
                   $lang = "en";
               }
               
               echo json_encode([
                   "state" => [
                       "error" => false,
                       "code" => 200,
                       "message" => ""
                   ],
                   "data" => json_decode(file_get_contents(__DIR__."/../helpers/$lang.json"))
                ]);
               
           } else {
               echo json_encode(ErrorMS::PARAMS);
           }
       }
   }
?>