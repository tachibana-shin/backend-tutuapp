<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
   
   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/OAuth.php";
   require_once __DIR__."/../modules/Method.php";
   /* Map SQL: AppsVIP
      ------------------------------------
      | id | idFor |
      ------------------------------------
   
   $SQL -> query("create table if not exists AppsVIP (
         id int not null auto_increment,
         idFor int not null,
         primary key(id)
   ");*/
   new class {
      public function __construct() {
         $method = strtoupper($_POST["action"] ?? $_SERVER["REQUEST_METHOD"]);
         
         switch ( $method ) {
            case "GET":
               $this -> read();
               break;
            default:
               if ( OAuth::authorized() ) {
                  switch ( $method ) {
                     case "POST":
                        $this -> write();
                        break;
                  }
               } else {
                  echo json_encode(ErrorMS::LOGIN);
               }
         }
      }
      
      private function read() {
         
         echo json_encode([
            "state" => [
               "error" => false,
               "code" => 200,
               "message" => ""
            ],
            "data" => Method::fetch_query("select name, id, icon from Apps order by view desc limit 20", ["icon"])
         ]);
      }
      private function write() {
         global $SQL;
         
         $SQL -> query("truncate AppsVIP");
         
         if ( is_array($_POST["id-for"] ?? null) ) {
            foreach ( $_POST["id-for"] as $index => $id ) {
               $SQL -> query("insert into AppsVIP (idFor) values (".((int) $id).")");
            }
            
            if ( $SQL -> error ) {
               echo json_encode(ErrorMS::UNKNOWN);
            } else {
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => ""
                  ],
                  "data" => null
               ]);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
   }
?> 