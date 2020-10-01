<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
    
   require_once __DIR__."/../modules/ErrorMS.php";
   require_once __DIR__."/../modules/SQL.php";
   
   new class {
      public function __construct() {
         global $SQL;
         if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
            if ( ($_GET["id"] ?? null) != null ) {
               
               if ( !isset($_COOKIE["app".$_GET["id"]]) ) {
                  $SQL -> query("update Apps set download = download + 1 where id = ".((int) $_GET["id"]));
                  setcookie("app".$_GET["id"], true, strtotime("+12hours"), "/", NULL, NULL, true);
                  if ( $SQL -> error ) {
                     echo json_encode(ErrorMS::UNKNOWN);
                  } else {
                     echo json_encode([
                        "state" => [
                           "error" => false,
                           "code" => 200,
                           "message" => ""
                        ],
                        "message" => null
                     ]);
                  }
               } else {
                  echo json_encode([
                     "state" => [
                        "error" => true,
                        "state" => 200,
                        "message" => "Block request because server find spam."
                     ],
                     "data" => null
                  ]);
               }
            } else {
               echo json_encode(ErrorMS::PARAMS);
            }
         }
      }
   }
   
?>