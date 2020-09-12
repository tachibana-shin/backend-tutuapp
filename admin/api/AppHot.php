<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/Method.php";
   
   new class {
      public function __construct() {
         switch ( $_SERVER["REQUEST_METHOD"] ) {
            case "GET":
               $this -> read();
         }
      }
      private function read() {
         global $SQL;
         
         $result = $SQL -> query("select name, id, icon from Apps order by download desc limit 8");
            
         $apps = [];
            
         if ( $result -> num_rows > 0 ) {
            while ( $row = $result -> fetch_array() ) {
               Method::unset_cache($row);
               $row["icon"] = File::get($row["icon"]);
               array_push($apps, $row);
            }
            $result -> free_result();
         }
         
         echo json_encode([
            "state" => [
               "error" => false,
               "code" => 200,
               "message" => ""
            ],
            "data" => $apps
         ]);
      }
   }
?> 