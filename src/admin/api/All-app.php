<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
   header("Content-Type: application/json; charset=utf-8");
   
   require_once __DIR__."/../modules/SQL.php";
   require_once __DIR__."/../modules/File.php";
   require_once __DIR__."/../modules/Method.php";
   
   new class {
      public function __construct() {
         $this -> read();
      }
      private function read() {
         global $SQL;
         
         $query = "select id, icon, name, download, developer, category, updated from Apps ";
         
         $q = $_GET["query"] ?? null;
         $category = $_GET["category"] ?? null;
         $sortby = $_GET["sort-by"] ?? null;
         
         $notWhere = true;
         
         if ( !empty($q) ) {
            $q = addslashes($q);
            $query .= "where (name like '%$q%' or developer like '%$q%') ";
            $notWhere = false;
         }
         if ( !empty($category) ) {
            $category = addslashes($category);
            if ( $notWhere ) {
               $query .= "where ";
            } else {
               $query .= "and ";
            }
            $query .= "category = '$category' ";
         }
         
         if ( !empty($sortby) ) {
            switch ( strtoupper($sortby) ) {
               case "A-Z":
                  $query .= "order by name asc";
                  break;
               case "Z-A":
                  $query .= "order by name desc";
                  break;
               case "DOWN":
                  $query .= "order by updated asc";
                  break; 
               case "UP":
                  $query .= "order by updated desc";
                  break;
            }
         }
         
         $query .= " limit 20 offset ".(((int) $_GET["page"] ?? 0) * 20);
         
         if ( $result = $SQL -> query($query) ) {
            
            $data = Method::fetch_array($result, ["icon"]);
            
            echo json_encode([
               "state" => [
                  "error" => false,
                  "code" => 200,
                  "message" => "Load success"
               ],
               "data" => $data
            ]);
         } else {
            echo json_encode([
               "state" => [
                  "error" => true,
                  "code" => 200,
                  "message" => ""
               ],
               "data" => null
            ]);
         }
         
      }
   }
   
?>