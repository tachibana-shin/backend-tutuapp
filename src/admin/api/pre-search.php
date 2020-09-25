<?php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Max-Age: 1000");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

   require_once __DIR__."/../modules/ErrorMS.php";
   require_once __DIR__."/../modules/Method.php";
   
   $SQL -> query("create table if not exists Keywords (
         id int null null,
         keyword tinytext not null,
         idApp int not null,
         point int not null,
         primary key(id)
      )");
   
   new class {
      public function __construct() {
         global $SQL;
         if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
            if ( ($_GET["query"] ?? null) != null ) {
               
               $apps = Method::fetch_query("select name, id from Apps where name like '%%1%'", [], $_GET["query"]);
               
               $max = Method::getAppMaxPoint($apps, $_GET["query"]);
               
               $SQL -> query("insert into Keywords ( keyword, idApp, point ) values ( '".addslashes($_GET["query"])."', $max[ID], $max[point] )");
               
               echo json_encode([
                  "state" => [
                     "error" => false,
                     "code" => 200,
                     "message" => ""
                  ],
                  "data" => $apps
               ]);
            } else {
               echo json_encode(ErrorMS::PARAMS);
            }
         }
      }
   }
   
?> 