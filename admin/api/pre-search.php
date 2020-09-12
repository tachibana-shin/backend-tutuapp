<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

   require_once __DIR__."/../modules/ErrorMS.php";
   require_once __DIR__."/../modules/Method.php";
   
   new class {
      public function __construct() {
         global $SQL;
         if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
            if ( ($_GET["query"] ?? null) != null ) {
               
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => ""
                  ],
                  "data" => Method::fetch_query("select name, id from Apps where name like '%%1%'", $_GET["query"])
               ]);
            } else {
               echo json_encode(ErrorMS::PARAMS);
            }
         }
      }
   }
   
?> 