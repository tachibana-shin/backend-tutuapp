<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
 
   require_once __DIR__."/../modules/OAuth.php";
   
   new class {
      public function __construct() {
         if ( OAuth::authorized() ) {
            $this -> Logout();
         } else {
            echo json_encode(ErrorMS::LOGIN);
         }
      }
      
      private function Logout() {
         if ( OAuth::Logout() ) {
            echo json_encode([
               "state" => [
                  "error" => false,
                  "code" => 200,
                  "message" => "Logout success"
               ],
               "data" => null
            ]);
         } else {
            echo json_encode(ErrorMS::UNKNOWN);
         }
      }
   }
?>