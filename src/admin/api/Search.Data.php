<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
    
   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/ErrorMS.php";
   require_once __DIR__."/../modules/Method.php";
   /* Map SQL: Keywords
      ------------------------------------
      | id | value | count |
      ------------------------------------
   
   */
   
   new class {
      public function __construct() {
         if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
            $this -> read();
         }
      }
      
      private function read() {
         $apps = Method::fetch_query("select id, name, icon from Apps order by view desc limit 8", ["icon"]);
         
         echo json_encode([
            "state" => [
               "error" => false,
               "code" => 200,
               "message" => ""
            ],
            "data" => [
               "intersting" => $apps,
               "keyword" => array_unique(array_slice(array_map(function ($item) {
                   return $item["name"];
               }, $apps), 0, 5))
            ]
         ]);
      }
   }
?>