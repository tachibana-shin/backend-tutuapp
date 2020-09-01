<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
 
   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/BASE_URI.php";
   require_once __DIR__."/../modules/OAuth.php";
   require_once __DIR__."/../modules/ErrorMS.php";
   /* Map SQL: Banners
      ------------------------------------
      | id | path | href | category |
      ------------------------------------
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
                     case "PUT":
                        $this -> update();
                        break;
                     case "DELETE":
                        $this -> remove();
                        break;
                  }
               } else {
                  echo json_encode(ErrorMS::LOGIN);
               }
               
         }
      }
      
      private function checkValid($key) {
         return !empty($_POST[$key] ?? null);
      }
       
      public function read() {
         global $SQL;
         $type = $_GET["type"] ?? null;
         
         if ( $type != null ) {
            $result = $SQL -> query("select path, href from Banners where category = '".addslashes($type)."' order by id asc");
            $banners = [];
            
            if ( $result -> num_rows > 0 ) {
               while ( $row = $result -> fetch_array() ) {
                  array_push($banners, [
                     "poster" => File::get($row["path"]),
                     "href" => $row["href"]
                  ]);
               }
               $result -> free_result();
               
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => null
                  ],
                  "data" => $banners
               ]);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
            
      }
      public function write() {
         global $SQL;
         
         if (
            $this -> checkValid("type")&&
            is_array($_FILES["banners"]["name"]) && 
            array_search(4, $_FILES["banners"]["error"]) == null
         ) {
            $result = [];
            foreach ( $_FILES["banners"]["name"] as $index => $name ) {
               $path = File::save("banners/", ".".pathinfo($name, PATHINFO_EXTENSION), $_FILES["banners"]["tmp_name"][$index]);
               array_push($result, $SQL -> query("insert into Banners (path, href, category) values ('".addslashes($path)."', '".($_POST["href"][$index] ?? "")."', '".addslashes($_POST["type"])."')"));
            }
            
            echo json_encode([
               "state" => [
                  "error" => empty(array_search(false, $result)),
                  "code" => 200,
                  "message" => $result
               ],
               "data" => null
            ]);
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
      public function update() {
         global $SQL;
         
         $id = $_POST["id"] ?? null;
         
         if ( $id != null && (int) $id != null ) {
            $id = (int) $id;
            
            $result = $SQL -> query("select path from Banners where id = $id");
            
            $proto = [];
            if ( isset($_POST["href"]) ) {
               array_push($proto, "href = '".addslashes($_POST["href"])."'");
            }
            
            if ( $result -> num_rows > 0 ) {
               
               if ( ($_FILES["banner"]["error"] ?? -1) == 0 ) {
                  $banner = $result -> fetch_array();
                  $result -> free_result();
                     
                  File::remove($banner["path"]);
                  
                  array_push($proto, "path = '".addslashes(File::save("banners/", ".".pathinfo($_FILES["banners"]["name"], PATHINFO_EXTENSION), $_FILES["banners"]["tmp_name"]))."'");
               }
               
               echo $SQL -> query("update Banners set ".join(", ", $proto)) ? json_encode([
                     "state" => [
                        "error" => false,
                        "code" => 200,
                        "message" => "Success"
                     ],
                     "data" => null
                  ]) :
                  json_encode(ErrorMS::UNKNOWN);
               
            } else {
               echo json_encode(ErrorMS::APPLICATION_INVALID);
            }
            
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
      public function remove() {
         global $SQL;
         
         $id = $_POST["id"] ?? null;
         
         if ( $id != null && (int) $id != null ) {
            $id = (int) $id;
            
            $result = $SQL -> query("select path from Banners where id = $id");
            
            if ( $result -> num_rows > 0 ) {
               $banner = $result -> fetch_array();
               $result -> free_result();
               
               File::remove($banner["path"]);
               
               if ( $SQL -> query("delete Banners where id = $id") ) {
                  echo json_encode([
                     "state" => [
                        "error" => true,
                        "code" => 200,
                        "message" => "Delete success."
                     ],
                     "data" => null
                  ]);
               } else {
                  echo json_encode(ErrorMS::UNKNOWN);
               }
            } else {
               echo json_encode(ErrorMS::APPLICATION_INVALID);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
   }
?>