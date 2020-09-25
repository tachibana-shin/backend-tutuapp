<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

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
         
         $keywords = Method::fetch_query("select value from keywords order count desc limit 5");
         $appHotPro = Method::fetch_array("select icon app.name, app.icon from app Apps, ");
      }
   }
?>