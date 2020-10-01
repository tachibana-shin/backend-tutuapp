<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");

   require_once __DIR__."/../modules/Method.php";
   
   new class {
      public function __construct() {
         switch ( $_SERVER["REQUEST_METHOD"] ) {
            case "GET":
               $this -> read();
         }
      }
      private function read() {
         switch( $_GET["type"] ?? "pro" ) {
            case "pro": {
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => ""
                  ],
                  "data" => Method::fetch_query("select name, id, icon from Apps order by download desc limit 8", ["icon"])
               ]);
               break;
            }
            default: {
               $type = $_GET["type"];
               $not = $type[0] == "^";
               $offset = $_GET["offset"] ?? 0;
               
               if ( $not ) {
                  $type = substr($type, 1);
               }
               
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => ""
                  ],
                  "data" => Method::fetch_query("select name, id, icon from Apps where ${$not ? 'not' : ''} category = '%1' order by download desc limit 20 offset %2", ["icon"], $type, $offset)
               ]);
               
            }
         }
      }
   }
?> 