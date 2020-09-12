<?php
   
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/BASE_URI.php";
   require_once __DIR__."/../modules/ErrorMS.php";
   require_once __DIR__."/../modules/Method.php";
   
   new class {
      public function __construct() {
         if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
            $this -> search();
         }
      }
      private function search() {
         if ( ($_GET["query"] ?? null) != null ) {
            echo json_encode([
               "state" => [
                  "error" => false,
                  "code" => 200,
                  "message" => ""
               ],
               "data" => Method::fetch_query("select icon, id, name from Apps where name like '%%1%' or developer like '%%1%' or category like '%%1%' or compatibility like '%%1%' or description like '%%1%' limit 20 offset %2", ["icon"], $_GET["query"], (int) ( $_GET["offset"] ?? 0 ))
            ]);
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
   }
?>