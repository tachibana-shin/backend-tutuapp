<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/OAuth.php";
   require_once __DIR__."/../modules/Method.php"; 
   require_once __DIR__."/../modules/ErrorMS.php";
   /* Map SQL: Banners
      ------------------------------------
      | id | path | category | url | uploaded |
      ------------------------------------
   
   $SQL -> query("create table if not exists Banners (
      id int not null auto_increment,
      path tinytext not null,
      category tinytext not null,
      url tinytext not null,
      uploaded timestamp not null default current_timestamp,
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
         
         if ( !empty($_GET["category"] ?? null) ) {
            if ( $result = $SQL -> query("select id, path, url from Banners where category = '".addslashes($_GET["category"])."' order by uploaded desc") ) {
               $banners = [];
               if ( $result -> num_rows > 0 ) {
                  while ( $row = $result -> fetch_array() ) {
                     Method::unset_cache($row);
                     $row["path"] = File::get($row["path"]);
                     array_push($banners, $row);
                  }
                  $result -> free_result();
               }
               
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => "Get success"
                  ],
                  "data" => $banners
               ]);
            } else {
               echo json_encode(ErrorMS::UNKNOWN);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
      private function write() {
         global $SQL;
         
         if ( !empty($_POST["category"] ?? null) && is_array($_POST["banners-map"] ?? null) ) {
            $result = $SQL -> query("select id, path, url, category from Banners where category = '".addslashes($_POST["category"])."'");
            $bannersInDB = [];
            if ( $result -> num_rows > 0 ) {
               while ( $row = $result -> fetch_array() ) {
                  Method::unset_cache($row);
                  array_push($bannersInDB, $row);
               }
               $result -> free_result();
            }
            
            $post = $_POST;
            $post["banners-path"] = array_map(function ($value) {
               return File::real_path($value);
            }, $post["banners-path"] ?? []);
            
            foreach ( $bannersInDB as $banner ) {
               if ( !in_array($banner["path"], $post["banners-path"]) ) {
                  File::remove($banner["path"]);
               }
            }
            // clear data done
            
            $index1 = 0;
            $index2 = 0;
            $index3 = 0;
            foreach ( $post["banners-map"] as $index => $value ) {
               
               $url = $post["banners-url"][$index2++] ?? null;
               if ( $value == 0 ) { 
                  $path = $post["banners-path"][$index1++];
               } else if ( $value == 1 ) {
                  $path = File::save("banners/", ".".pathinfo($_FILES["banners"]["name"][$index3], PATHINFO_EXTENSION), $_FILES["banners"]["tmp_name"][$index3++]);
               }

               if ( isset($bannersInDB[$index]) ) {
                  if ( $path != $bannersInDB[$index]["path"] || $url != $bannersInDB[$index]["url"] ) {
                     // bất công vcl
                     # @update
                     $SQL -> query($query = "update Banners set path = '".addslashes($path)."', url = '".addslashes($url)."', uploaded = current_timestamp where id = ".$bannersInDB[$index]["id"]);
                  }
               } else {
                  # @insert into
                  $SQL -> query("insert into Banners ( path, category, url ) values ( '".addslashes($path)."', '".addslashes($_POST["category"])."', '".addslashes($url)."' )"); 
               }
            }
            
            $index = count($post["banners-map"]);
            
            /* delete item */
            $result = $SQL -> query("select id, path from Banners order by id asc limit 99999999 offset $index");
            
            while ( $row = $result -> fetch_array() ) {
               File::remove($row["path"]);
               $SQL -> query("delete from Banners where id = $row[id]");
            }
            
            $result -> free_result();
            
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