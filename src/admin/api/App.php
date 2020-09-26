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
   require_once __DIR__."/../modules/Method.php"; 
   require_once __DIR__."/../modules/ErrorMS.php";
   /* Map SQL: Apps
      ------------------------------------
      | id | name | icon | size | description | screenshot <sezialise> | developer | category | updated | compatibility | languages | version | account | download |
      ------------------------------------
   
   $SQL -> query("create table if not exists Apps (
      id int not null auto_increment,
      name tinytext not null,
      icon tinytext not null,
      size tinytext not null,
      description text not null,
      screenshot text not null,
      developer tinytext not null,
      category tinytext not null,
      updated timestamp not null default current_timestamp,
      compatibility tinytext not null,
      languages text not null,
      version text not null,
      account tinytext not null,
      view int not null default = 0,
      download int not null default = 0,
 key(id)
   )");
   echo $SQL -> error;
   */
   
   //header("Content-Type: application/json");
   
   new class {
      const NAME_COLUMN = ["name", "icon", "size", "description", "screenshot", "developer", "category", "compatibility", "languages", "version", "account"];
            
      
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
      
      private function read() {
         global $SQL;
         $id = $_GET["id"] ?? null;
         
         if ( $id != null && (int) $id != null ) {
            $id = (int) $id;
            
            $result = $SQL -> query("select * from Apps where id = $id");
            
            if ( $result -> num_rows > 0 ) {
               $app = $result -> fetch_array();
               $result -> free_result();
               
               $app["version"] = unserialize($app["version"]);
               $app["languages"] = unserialize($app["languages"]);
               
	            $app["icon"] = File::get($app["icon"]);
               
               $app["screenshot"] = array_map(
                     function ($file) {
                        return File::get($file);
                     },
                     unserialize(
                        $app["screenshot"]
                     ) ?: []
                  );
        
               Method::unset_cache($app);
               
               if( $_GET["produce"] ?? false ) {
                  
                  $appsViewed = @json_encode($_COOKIE["viewd"] ?? "[]") ?: [];
                  
                  if ( $appsViewed[$app["id"]] ?? false ) {
                     $SQL -> query("update from Apps set view = view + 1 where id = $app[id]");
                     array_push($appsViewed, $app["id"]);
                     setcookie("viewd", json_encode($appsViewed), "/");
                  }
                  
                  echo json_encode([
                     "state" => [
                        "error" => false,
                        "code" => 200,
                        "message" => ""
                     ],
                     "data" => [
                        "data" => $app,
                        "apps" => Method::fetch_query("select name, icon from Apps where not id = $id and category = '%1' order by updated desc limit 8", ["icon"], $app["category"])
                     ]
                  ]);
                  echo $SQL-> error;
               } else {
                  echo json_encode([
                     "state" => [
                        "error" => false,
                        "data" => 200,
                        "message" => ""
                     ],
                     "data" => $app
                  ]);
               }
            } else {
               echo json_encode(ErrorMS::APPLICATION_INVALID);
            }
            
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
      
      private function saveIcon() {
         return File::save("icon/", ".".pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION), $_FILES["icon"]["tmp_name"]);
      }
      private function saveScreenshot() {
         $result = [];
         foreach ( $_FILES["screenshot"]["name"] as $index => $name ) {
            if ( $_FILES["screenshot"]["error"][$index] == 0 ) {
               array_push($result, File::save(
               "screenshot/",
               ".".pathinfo($name, PATHINFO_EXTENSION), 
               $_FILES["screenshot"]["tmp_name"][$index]
               ));
            }
         }
         return $result;
      }
      private function convertVersion() {
         $versions = [];
         foreach ( $_POST["name-vers"] as $index => $name ) {
            array_push($versions, [
               "name" => $name,
               "value" => $_POST["url-vers"][$index] ?? null
            ]);
         }
         return $versions;
      }
      private function write() {
         global $SQL;
         
         if (
            $this -> checkValid("name") &&
            $_FILES["icon"]["error"] == 0 &&
            $this -> checkValid("account") &&
            $this -> checkValid("description") &&
            is_array($_FILES["screenshot"]["error"] ?? null) &&
            $this -> checkValid("category") &&
            is_array($_POST["name-vers"] ?? null) &&
            is_array($_POST["url-vers"] ?? null)
         ) {
            $post = $_POST;
            $post["icon"] = $this -> saveIcon();
            $post["screenshot"] = $this -> saveScreenshot();
            $post["version"] = $this -> convertVersion();
            
            $keyAccess = array_filter(self::NAME_COLUMN, function($value) use ($post) {
               return isset($post[$value]);
            });
            
            if ( $SQL -> query("insert into Apps (".join(", ", $keyAccess).") values (".join(", ", array_map(function($value) use ($post) {
                  $value = $post[$value];
                  switch ( gettype($value) ) {
                     case "string":
                        return "'".addslashes($value)."'";
                     case "array":
                        return "'".serialize($value)."'";
                     default:
                        return $value;
                  }
            }, $keyAccess)).")") ) {
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => "Save success."
                  ],
                  "data" => [
                     "id" => $SQL -> insert_id
                  ]
               ]);
            } else {
               echo json_encode(ErrorMS::UNKNOWN);
            }
         } else {
            echo json_encode(ErrorMS::PARAMS);
         }
      }
      private function update() {
         global $SQL;
         
         $id = $_POST["id"] ?? null;
         
         if ( $id != null && (int) $id != null ) {
            $id = (int) $id;
            $post = $_POST;
            
            $result = $SQL -> query("select icon, screenshot from Apps where id = $id");
            
            if ( $result -> num_rows > 0 ) {
               $app = $result -> fetch_array();
               $result -> free_result();
               
               if ( isset($_FILES["icon"]) && $_FILES["icon"]["error"] == 0 ) {
                  
                  File::remove($app["icon"]);
                  
                  $post["icon"] = $this -> saveIcon();
                  
               }
               
               if ( is_array($_POST["screenshot-map"] ?? null) ) {
                  $lengthMap = count($_POST["screenshot-map"]);
                  $length = count(unserialize($app["screenshot"]));
                  
                  // format screenshot-path
                  
                  $post["screenshot-path"] = array_map(function ($value) {
                     return File::real_path($value);
                  }, $post["screenshot-path"] ?? []);
                  
                  foreach ( unserialize($app["screenshot"]) as $value ) {
                     if ( !in_array($value, $post["screenshot-path"]) ) {
                        File::remove($value);
                     }
                  }
                  
                  // clear file done
                  
                  $screenshot = [];
                  $index1 = 0;
                  $index2 = 0;
                  
                  foreach ( $post["screenshot-map"] as $value ) {
                     if ( $value == 0 ) {
                        array_push($screenshot, $post["screenshot-path"][$index1++]);
                     } else if ( $value == 1 && $_FILES["screenshot"]["error"][$index2] == 0 ) {
                        array_push($screenshot, File::save("screenshot/", ".".pathinfo($_FILES["screenshot"]["name"][$index2], PATHINFO_EXTENSION), $_FILES["screenshot"]["tmp_name"][$index2++]));
                     }
                  }
                  
                  $post["screenshot"] = $screenshot;
               }
               
               // update to database
                  
               $columnExists = array_filter(self::NAME_COLUMN, function($column) use ($post) {
                     return !empty($post[$column] ?? null);
               });
               $_this = $this;
               
               if ( $SQL -> query($query = "update Apps set ".join(", ", array_map(function ($column) use ($post, $_this) {
                     if ( $column == "version" ) {
                        return "version = '".serialize($_this -> convertVersion())."'";
                     } else {
                        switch ( gettype($post[$column]) ) {
                           case "string":
                              return "$column = '".addslashes($post[$column])."'";
                              break;
                           case "array":
                              return "$column = '".serialize($post[$column])."'";
                              break;
                           default:
                              return "$column = '".$post[$column]."'";
                        }
                     }
                  }, $columnExists)).", updated = CURRENT_TIMESTAMP where id = $id") ) {
                     echo json_encode([
                        "state" => [
                           "error" => false,
                           "code" => 200,
                           "message" => "Update success."
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
      private function remove() {
         global $SQL;
         
         $id = $_POST["id"] ?? null;
         
         if ( $id != null && (int) $id != null ) {
            $id = (int) $id;
            
            
            $result = $SQL -> query("select icon, screenshot from Apps where id = $id");
            
            if ( $result -> num_rows > 0 ) {
               $app = $result -> fetch_array();
               $result -> free_result();

               File::remove($app["icon"]);
               foreach ( unserialize($app["screenshot"]) as $value ) {
                  File::remove($value);
               }
               
               if ( $SQL -> query("delete from Apps where id = $id") ) {
                  echo json_encode([
                     "state" => [
                        "error" => false,
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
