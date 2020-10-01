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
   require_once __DIR__."/../modules/ErrorMS.php";
   /* Map SQL: iCloud
      ------------------------------------
      | id | username | password |
      ------------------------------------
   
   $SQL -> query("create table if not exists iCloud (
      id int not null auto_increment,
      username tinytext not null,
      password tinytext not null,
      primary key(id)
   )");
   */
   
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
               }
         }
         
      }
      
      private function read() {
         global $SQL;
         
         if ( $result = $SQL -> query("select username, password from iCloud") ) {
            $icloud = [];
            if ( $result -> num_rows > 0 ) {
               while ( $row = $result -> fetch_array() ) {
                  Method::unset_cache($row);
                  array_push($icloud, $row);
               }
               $result -> free_result();
            }
            
            echo json_encode([
               "state" => [
                  "error" => false,
                  "code" => 200,
                  "message" => "Load success."
               ],
               "data" => $icloud
            ]);
         } else {
            echo json_encode(ErrorMS::UNKNOWN);
         }
      }
      private function write() {
         global $SQL;
         
         if (
            is_array($_POST["username"] ?? null) &&
            is_array($_POST["password"] ?? null) &&
            count($_POST["username"]) == count($_POST["password"])
         ) {
            $SQL -> query("truncate iCloud");
            foreach ( $_POST["username"] as $index => $name ) {
               $SQL -> query("insert into iCloud ( username, password ) values ( '".addslashes($name)."', '".addslashes($_POST["password"][$index])."' )");
            }
            if ( $SQL -> error ) {
               echo json_encode(ErrorMS::UNKNOWN);
            } else {
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => "Save success"
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